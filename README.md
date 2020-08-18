# 微信小程序SDK

用于PHP方便调用微信小程序的 [服务端接口](https://developers.weixin.qq.com/miniprogram/dev/api-backend/)

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
