# 微信小程序 SDK

用于 PHP 方便调用微信小程序的 [服务端接口](https://developers.weixin.qq.com/miniprogram/dev/api-backend/)

[![Latest Stable Version](https://poser.pugx.org/luckyhjh/wechat-mini-program/v)](//packagist.org/packages/luckyhjh/wechat-mini-program)
[![Total Downloads](https://poser.pugx.org/luckyhjh/wechat-mini-program/downloads)](//packagist.org/packages/luckyhjh/wechat-mini-program)
[![Latest Unstable Version](https://poser.pugx.org/luckyhjh/wechat-mini-program/v/unstable)](//packagist.org/packages/luckyhjh/wechat-mini-program)
[![License](https://poser.pugx.org/luckyhjh/wechat-mini-program/license)](//packagist.org/packages/luckyhjh/wechat-mini-program)

## 接入要求

"php": ">=5.4"

## 接入方式

### 1、composer 安装

composer require luckyhjh/wechat-mini-program

### 2、新建文件

新建类继承 WechatMiniProgram，并实现相关接口，token 可保存在 redis 或 mysql 等。

例如 Laravel/Lumen，可新建文件 app/Libs/WechatMiniProgram.php：

```php
namespace App\Libs;

use Illuminate\Support\Facades\Cache;
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
    public function getAccessToken()
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
            $this->setAccessToken($access_token, $Token->expires_in);
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
    public function setAccessToken($access_token, $expire = 7200)
    {
        $cache_name = 'wx_token_' . $this->getAppid();
        return Cache::set($cache_name, $access_token, $expire);
    }
}
```

例如 ThinkPHP，可新建文件 extend/WechatMiniProgram.php：

```php
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
    public function getAccessToken()
    {
        $cache_name = 'wx_token_' . $this->getAppid();
        $access_token = cache($cache_name);
        if ($access_token) {
            return $access_token;
        }

        $AccessToken = new AccessToken($this);
        try {
            $Token = $AccessToken->getAccessToken();
            $access_token = $Token->access_token;
            $this->setAccessToken($access_token, $Token->expires_in);
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
    public function setAccessToken($access_token, $expire = 7200)
    {
        $cache_name = 'wx_token_' . $this->getAppid();
        return cache($cache_name, $access_token, $expire);
    }
}
```

### 3、业务调用

业务里根据需要调用

```php
$mp = new WechatMiniProgram();
$Auth = new Auth($mp);
$User = new \WechatMiniProgram\Api\User($mp);//如果要调用其它接口，可复用$mp
try {
    $session = $Auth->code2session($code);
    $openid = $session->openid;
} catch (ApiException $apiException) {
}
```

## 支持接口

- AccessToken->getAccessToken()：[获取小程序全局唯一后台接口调用凭据](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/access-token/auth.getAccessToken.html)
- Auth->code2session()：[用 code 获取 openid](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html)
- User->getPhoneNumber()：[获取手机号](https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html)
- SubscribeMessage->send()：[发送订阅消息](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html)
- WxaCode->getUnlimited()：[生成小程序码，可接受页面参数较短，生成个数不受限](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html)
- WxaCode->get()：[生成小程序码，可接受 path 参数较长，生成个数受限](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.get.html)
- SecCheck->messageIsRisky()：[文本是否为风险内容](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.msgSecCheck.html)
- SecCheck->imageIsRisky()：[文本是否为风险内容](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.imgSecCheck.html)

## License

MIT
