<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;

/**
 * 小程序码
 * Class WxaCode
 * @package MiniProgram\Api
 */
class WxaCode extends ApiBase
{
    /**
     * 生成小程序码，可接受页面参数较短，生成个数不受限，永久有效。实际效果类似于 pages/index/index?scene=XXX
     * @param string $scene 参数（小程序获取参数方法：scene = decodeURIComponent(query.scene); 如获取到"A=1&B=2"，要JS自己再拆开处理）
     * @param string $page 页面路径，必须是已经发布的小程序存在的页面。例如 pages/index/index，留空就默认跳首页
     * @param int $width 二维码的宽度，单位 px
     * @param bool $is_hyaline 是否需要透明底色
     * @param bool $auto_color 自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调，默认 false
     * @param array $line_color auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     * @return string 图片二进制内容
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html
     */
    public function getUnlimited($scene, $page = '', $width = 430, $is_hyaline = false, $auto_color = false, $line_color = [])
    {
        preg_match("/[\w!#$&'()*+,\/\:;=?@\-._~]+/", $scene, $matches);
        $length = strlen($scene);
        if (empty($scene) || $matches[0] != $scene || $length > 32) {
            $msg = 'scene was invalid';
            $this->addError($msg, [
                'scene' => $scene,
                'matches' => $matches,
                'length' => $length,
            ]);
            throw new ApiException($msg, 422);
        }

        $page = ltrim($page, '/');
        if ($page && preg_match("/[=?]+/", $page)) {
            $msg = 'page was invalid';
            $this->addError($msg, [
                'page' => $page,
            ]);
            throw new ApiException($msg, 422);
        }

        empty($width) and $width = 430;
        if ($width < 280 || $width > 1280) {
            $msg = 'width was invalid';
            $this->addError($msg, [
                'width' => $width,
            ]);
            throw new ApiException($msg, 422);
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/wxa/getwxacodeunlimit?access_token={$accessToken}";
        $data = [
            'scene'  => $scene,
            'page'  => $page,
            'width'  => $width,
            'is_hyaline'  => $is_hyaline,
            'auto_color'  => $auto_color,
            'line_color'  => $line_color,
        ];
        $res = $this->curl('JSON', $url, $data);

        return $this->response($res);
    }

    /**
     * 生成小程序码，可接受 path 参数较长，生成个数受限，永久有效。
     * @param string $path 页面路径，可带参，如 pages/index/index?foo=bar
     * @param int $width 二维码的宽度，单位 px
     * @param bool $is_hyaline 是否需要透明底色
     * @param bool $auto_color 自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调，默认 false
     * @param array $line_color auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     * @return string 图片二进制内容
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.get.html
     */
    public function get($path, $width = 430, $is_hyaline = false, $auto_color = false, $line_color = [])
    {
        $length = strlen($path);
        if (empty($path) || $length > 128) {
            $msg = 'path was invalid';
            $this->addError($msg, [
                'path' => $path,
                'length' => $length,
            ]);
            throw new ApiException($msg, 422);
        }

        empty($width) and $width = 430;
        if ($width < 280 || $width > 1280) {
            $msg = 'width was invalid';
            $this->addError($msg, [
                'width' => $width,
            ]);
            throw new ApiException($msg, 422);
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/wxa/getwxacode?access_token={$accessToken}";
        $data = [
            'path'  => $path,
            'width'  => $width,
            'is_hyaline'  => $is_hyaline,
            'auto_color'  => $auto_color,
            'line_color'  => $line_color,
        ];
        $res = $this->curl('JSON', $url, $data);

        return $this->response($res);
    }

    /**
     * 处理响应结果
     * @param string $res
     * @return mixed
     * @throws ApiException
     */
    private function response($res)
    {
        $error = json_decode($res, true);
        if ($error) {
            $code = isset($error['errcode']) ? $error['errcode'] : 9999;
            $msg = isset($error['errmsg']) ? $error['errmsg'] : 'httpRequest failed';
            $this->addError($msg, $error);
            throw new ApiException($msg, $code);

        } else {
            //header("content-type: image/jpeg");
            //echo $res;exit;
            return $res;
        }
    }
}
