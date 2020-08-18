<?php

namespace WechatMiniProgram\Model;


use WechatMiniProgram\ModelBase;

class PhoneNumber extends ModelBase
{
    /**
     * @var string
     */
    public $phoneNumber = '';

    /**
     * @var string
     */
    public $purePhoneNumber = '';

    /**
     * @var string
     */
    public $countryCode = '';

}
