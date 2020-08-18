<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;
use WechatMiniProgram\Model\Session;

class Auth extends ApiBase
{

    /**
     * 用code获取openid
     * @param string $mp_code 小程序生成的code
     * @return Session
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     */
    public function code2session($mp_code)
    {
        $appid = $this->miniProgram->getAppid();
        $secret = $this->miniProgram->getSecret();

        if (empty($appid) || empty($secret) || empty($mp_code)) {
            $msg = 'params error';
            $this->addError($msg, [
                'appid' => $appid,
                'secret' => $secret,
                'code' => $mp_code,
            ]);
            throw new ApiException($msg, 422);
        }

        $url = self::API_HOST . "/sns/jscode2session";
        $data = [
            'appid'  => $appid,
            'secret'  => $secret,
            'js_code'  => $mp_code,
            'grant_type'  => 'authorization_code',
        ];
        $res = $this->httpRequest('GET', $url, $data);
        //$res = ['session_key' => 'f9umDYdqoz0wgacCrLDYFA==', 'openid' => 'o4W6G5Fz0M5imbBAIFxSfmPMmKe8'];

        $code = isset($res['errcode']) ? $res['errcode'] : 0;
        $msg = isset($res['errmsg']) ? $res['errmsg'] : 'ok';
        $openid = isset($res['openid']) ? $res['openid'] : '';
        $session_key = isset($res['session_key']) ? $res['session_key'] : '';

        if ($code != 0 || empty($openid)) {
            $this->addError($msg, $res);
            throw new ApiException($msg, $code);
        }

        $Session = new Session();
        $Session->openid = $openid;
        $Session->session_key = $session_key;

        return $Session;
    }

}
