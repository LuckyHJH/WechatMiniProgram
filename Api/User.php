<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;
use WechatMiniProgram\Model\PhoneNumber;

class User extends ApiBase
{

    /**
     * 获取手机号
     * @param string $code
     * @return PhoneNumber
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/user-info/phone-number/getPhoneNumber.html
     */
    public function getPhoneNumber(string $code)
    {
        if (empty($code)) {
            $msg = 'params error';
            $this->addError($msg, [
                'code' => $code,
            ]);
            throw new ApiException($msg, 422);
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/wxa/business/getuserphonenumber?access_token={$accessToken}";
        $data = [
            'code'  => $code,
        ];
        $res = $this->httpRequest('JSON', $url, $data);

        $code = isset($res['errcode']) ? $res['errcode'] : 0;
        $msg = isset($res['errmsg']) ? $res['errmsg'] : 'ok';
        $phone_info = isset($res['phone_info']) ? $res['phone_info'] : [];

        if ($code != 0 || empty($phone_info)) {
            $this->addError($msg, $res);
            throw new ApiException($msg, $code);
        }

        $watermark_appid = isset($phone_info['watermark']['appid']) ? $phone_info['watermark']['appid'] : '';
        $appid = $this->miniProgram->getAppid();
        if ($watermark_appid != $appid) {
            $this->addError('appid not match', [
                'watermark_appid' => $watermark_appid,
                'appid' => $appid,
            ]);
            throw new ApiException($errCode, $errCode);
        }

        $PhoneNumber = new PhoneNumber();
        $PhoneNumber->phoneNumber = isset($phone_info['phoneNumber']) ? $phone_info['phoneNumber'] : '';
        $PhoneNumber->purePhoneNumber = isset($phone_info['purePhoneNumber']) ? $phone_info['purePhoneNumber'] : '';
        $PhoneNumber->countryCode = isset($phone_info['countryCode']) ? $phone_info['countryCode'] : '';
        return $PhoneNumber;
    }

}
