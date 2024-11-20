<?php

namespace WechatMiniProgram\Model;


use WechatMiniProgram\ModelBase;

class Session extends ModelBase
{
    /**
     * openid
     * @var string
     */
    public $openid = '';

    /**
     * session_key
     * @var string
     */
    public $session_key = '';
}
