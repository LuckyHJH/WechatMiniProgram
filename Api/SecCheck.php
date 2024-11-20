<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;

/**
 * 安全内容检测接口
 * Class SecCheck
 * @package MiniProgram\Api
 */
class SecCheck extends ApiBase
{
    /**
     * 文本是否为风险内容
     * @param string $content
     * @return bool
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.msgSecCheck.html
     */
    public function messageIsRisky($content)
    {
        if (empty($content)) {
            $msg = 'params error';
            $this->addError($msg, [
                'content' => $content,
            ]);
            throw new ApiException($msg, 422);
        }

        $max_length = 500 * 1024;//不超过500KB
        if (strlen($content) > $max_length) {
            $this->addError('message is too long', ['str_length' => strlen($content), 'max_length' => $max_length]);
            throw new ApiException('message is too long');
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/wxa/msg_sec_check?access_token={$accessToken}";
        $data = [
            'content'  => $content,
        ];
        $res = $this->httpRequest('JSON', $url, $data);

        return $this->response($res);
    }

    /**
     * 图片是否为风险内容
     * @param string $file 本地文件路径
     * @return bool
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/sec-check/security.imgSecCheck.html
     */
    public function imageIsRisky($file)
    {
        if (empty($file) || !is_file($file)) {
            $msg = 'params error';
            $this->addError($msg, [
                'file' => $file,
            ]);
            throw new ApiException($msg, 422);
        }

        //像素不超过750x1334
        list($width, $height) = getimagesize($file);
        if ($width > 750 || $height > 1334) {
            $this->addError('message is too long', ['width' => $width, 'height' => $height]);
            throw new ApiException('image is too large');
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/wxa/img_sec_check?access_token={$accessToken}";
        $data = [
            'media'  => $file,
        ];
        $res = $this->httpRequest('FILE', $url, $data);

        return $this->response($res);
    }

    /**
     * 处理响应结果
     * @param array $res
     * @return bool
     * @throws ApiException
     */
    private function response($res)
    {
        $code = isset($res['errcode']) ? $res['errcode'] : 9999;
        $msg = isset($res['errmsg']) ? $res['errmsg'] : 'httpRequest failed';

        if ($code == 0) {
            return false;

        } elseif ($code == 87014) {
            //当content内含有敏感信息，则返回87014
            return true;

        } else {
            $this->addError($msg, $res);
            throw new ApiException($msg, $code);
        }
    }
}
