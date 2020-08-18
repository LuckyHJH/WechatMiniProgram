<?php

namespace WechatMiniProgram\Model;


use WechatMiniProgram\ModelBase;

class Token extends ModelBase
{
    /**
     * 接口的凭证
     * @var string
     */
    public $access_token = '';

    /**
     * 凭证有效时间，单位：秒。目前是7200秒之内的值。
     * @var int
     */
    public $expires_in = 0;
}
