<?php

namespace WechatMiniProgram;

abstract class WechatMiniProgram
{
    private $appid = '';
    private $secret = '';

    const ERROR_ACCESS_TOKEN = 41001;

    public function __construct($appid = '', $secret = '')
    {
        !empty($appid) and $this->appid = $appid;
        !empty($secret) and $this->secret = $secret;
    }


    /**
     * 获取保存下来的access_token
     * @return string
     */
    abstract public function getApiToken();

    /**
     * 把获取到的access_token保存下来
     * @param string $access_token
     * @param int $expire
     * @return bool
     */
    abstract public function setApiToken($access_token, $expire = 7200);


    public function getAppid()
    {
        return $this->appid;
    }

    public function setAppid($appid)
    {
        $this->appid = $appid;
    }


    public function getSecret()
    {
        return $this->secret;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

}
