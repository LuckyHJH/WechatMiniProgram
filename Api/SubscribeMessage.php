<?php

namespace WechatMiniProgram\Api;

use WechatMiniProgram\ApiBase;
use WechatMiniProgram\ApiException;

/**
 * 订阅消息
 * Class SubscribeMessage
 * @package MiniProgram\Api
 */
class SubscribeMessage extends ApiBase
{
    /**
     * 发送订阅消息
     * @param string $openid 接收者的openid
     * @param string $template_id 所需下发的订阅模板id
     * @param array $data 模板内容，格式形如 { "key1": { "value": any }, "key2": { "value": any } }
     * @param string $page 点击模板卡片后的跳转页面（示例index?foo=bar）
     * @return bool 发送成功返回true，没有剩余推送次数或被拒绝就返回false
     * @throws ApiException
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
     */
    public function send($openid, $template_id, $data, $page = '')
    {
        if (empty($openid) || empty($template_id)) {
            $msg = 'params error';
            $this->addError($msg, [
                'openid' => $openid,
                'template_id' => $template_id,
            ]);
            throw new ApiException($msg, 422);
        }

        $accessToken = $this->miniProgram->getAccessToken();
        $url = self::API_HOST . "/cgi-bin/message/subscribe/send?access_token={$accessToken}";
        $data = [
            'touser'  => $openid,
            'template_id'  => $template_id,
            'data'  => $data,
        ];
        !empty($page) and $data['page'] = $page;
        $res = $this->httpRequest('JSON', $url, $data);

        $code = isset($res['errcode']) ? $res['errcode'] : 9999;
        $msg = isset($res['errmsg']) ? $res['errmsg'] : 'httpRequest failed';

        if ($code == 0) {
            return true;

        } elseif ($code == 43101) {
            //用户拒绝接受消息，如果用户之前曾经订阅过，则表示用户取消了订阅关系
            //或没有剩余推送次数
            return false;

        } else {
            $this->addError($msg, $res);
            throw new ApiException($msg, $code);
        }
    }

}
