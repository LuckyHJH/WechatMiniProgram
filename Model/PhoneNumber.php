<?php

namespace WechatMiniProgram\Model;


use WechatMiniProgram\ModelBase;

class PhoneNumber extends ModelBase
{
    /**
     * 用户绑定的手机号（国外手机号会有区号）
     * @var string
     */
    public $phoneNumber = '';

    /**
     * 没有区号的手机号
     * @var string
     */
    public $purePhoneNumber = '';

    /**
     * 区号
     * @var string
     */
    public $countryCode = '';

}
