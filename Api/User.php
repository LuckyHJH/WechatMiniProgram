<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\Aes\WxBizDataCrypt;
use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;
use WechatMiniProgram\Model\PhoneNumber;

class User extends ApiBase
{
    /**
     * 获取手机号
     * @param string $sessionKey 登录后获取的会话密钥
     * @param string $iv 与用户数据一同返回的初始向量
     * @param string $encryptedData 加密的用户数据
     * @return PhoneNumber
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html
     */
    public function getPhoneNumber($sessionKey, $iv, $encryptedData)
    {
        $appid = $this->miniProgram->getAppid();
        $pc = new WxBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode != 0) {
            $this->addError($errCode, [
                'sessionKey' => $sessionKey,
                'iv' => $iv,
                'encryptedData' => $encryptedData,
            ]);
            throw new ApiException($errCode, $errCode);
        }

        $data = json_decode($data, true);
        $watermark_appid = isset($data['watermark']['appid']) ? $data['watermark']['appid'] : '';
        if ($watermark_appid != $appid) {
            $this->addError('appid not match', [
                'watermark_appid' => $watermark_appid,
                'appid' => $appid,
            ]);
            throw new ApiException($errCode, $errCode);
        }

        $PhoneNumber = new PhoneNumber();
        $PhoneNumber->phoneNumber = isset($data['phoneNumber']) ? $data['phoneNumber'] : '';
        $PhoneNumber->purePhoneNumber = isset($data['purePhoneNumber']) ? $data['purePhoneNumber'] : '';
        $PhoneNumber->countryCode = isset($data['countryCode']) ? $data['countryCode'] : '';
        return $PhoneNumber;
    }

}
