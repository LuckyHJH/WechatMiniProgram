# 微信小程序SDK

用于PHP方便调用微信小程序的 [服务端接口](https://developers.weixin.qq.com/miniprogram/dev/api-backend/)

## 接入要求

"php": ">=5.4.0"

## 接入方式

以Lumen作为例子

### 下载安装
目录放到 app/Libs/WechatMiniProgram/

### 新建文件
新建类并继承 \WechatMiniProgram\WechatMiniProgram，如 app/Libs/WechatMiniProgram.php

```php
namespace App\Libs;

use WechatMiniProgram\Api\AccessToken;
use WechatMiniProgram\ApiException;

class WechatMiniProgram extends \WechatMiniProgram\WechatMiniProgram
{
    public function __construct($appid = '', $secret = '')
    {
        empty($appid) and $appid = env('WX_APPID');
        empty($secret) and $secret = env('WX_SECRET');
        parent::__construct($appid, $secret);
    }

    /**
     * 读取token
     * @return string
     */
    public function getApiToken()
    {
        $cache_name = 'wx_token_' . $this->getAppid();
        $access_token = Cache::get($cache_name);
        if ($access_token) {
            return $access_token;
        }

        $AccessToken = new AccessToken($this);
        try {
            $Token = $AccessToken->getAccessToken();
            $access_token = $Token->access_token;
            $this->setApiToken($access_token, $Token->expires_in);
            return $access_token;

        } catch (ApiException $apiException) {
            return '';
        }
    }

    /**
     * 保存token
     * @param string $access_token
     * @param int $expire
     * @return bool
     */
    public function setApiToken($access_token, $expire = 7200)
    {
        $cache_name = 'wx_token_' . $this->getAppid();
        return Cache::set($cache_name, $access_token, $expire);
    }
}
```

### 业务调用
业务里根据需要调用

```php
$mp = new \App\Libs\WechatMiniProgram();
$Auth = new Auth($mp);
$User = new User($mp);//如果要调用其它接口
try {
    $session = $Auth->code2session($code);
    $openid = $session->openid;
} catch (ApiException $apiException) {
}
```

### composer引用
在composer.json文件，在"autoload"的"psr-4"里，加上：

```
"WechatMiniProgram\\": "app/Libs/WechatMiniProgram"
```

然后composer update一下。


## 支持接口

- AccessToken->getAccessToken()：[获取小程序全局唯一后台接口调用凭据](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html)
- Auth->code2session()：[用code获取openid](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html)
- User->getPhoneNumber()：[获取手机号](https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html)
- SubscribeMessage->send()：[发送订阅消息](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html)
- WxaCode->getUnlimited()：[生成小程序码，可接受页面参数较短，生成个数不受限](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html)
- WxaCode->get()：[生成小程序码，可接受 path 参数较长，生成个数受限](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.get.html)
- SecCheck->messageIsRisky()：[文本是否为风险内容](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.msgSecCheck.html)
- SecCheck->imageIsRisky()：[文本是否为风险内容](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.imgSecCheck.html)
