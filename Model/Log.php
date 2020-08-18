<?php

namespace WechatMiniProgram\Model;


use WechatMiniProgram\ModelBase;

class Log extends ModelBase
{
    /**
     * 时间戳
     * @var int
     */
    public $time = 0;

    /**
     * 错误信息
     * @var string
     */
    public $msg = '';

    /**
     * 更多详细内容
     * @var array
     */
    public $data = [];
}
