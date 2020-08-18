<?php


namespace WechatMiniProgram;


use WechatMiniProgram\Model\Log;

class ApiBase
{
    protected $miniProgram;

    private $errors = [];

    const API_HOST = "https://api.weixin.qq.com";

    public function __construct(WechatMiniProgram $miniProgram)
    {
        $this->miniProgram = $miniProgram;
    }


    /**
     * 返回错误信息
     * 格式如下：[{"time":1343105555,"msg":"信息内容","data":{}}]
     * @return Log[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function addError($msg, $data = [])
    {
        $Log = new Log();
        $Log->time = time();
        $Log->msg = $msg;
        $Log->data = $data;
        $this->errors[] = $Log;
    }

    protected function delLastError()
    {
        array_pop($this->errors);
    }

    /**
     * HTTP请求
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $header
     * @return array
     * @throws ApiException
     */
    protected function httpRequest($method, $url, $data = [], $header = [])
    {
        $output = $this->curl($method, $url, $data, $header);

        $res = json_decode($output,true);
        empty($res) and $res = [];

        //access_token错误，那就重新设置
        if (isset($res['code']) && $res['code'] == WechatMiniProgram::ERROR_ACCESS_TOKEN) {
            $this->miniProgram->setApiToken('');
        }

        return $res;
    }

    /**
     * curl 返回原始响应内容
     * @param $method
     * @param $url
     * @param array $data
     * @param array $header
     * @return string
     * @throws ApiException
     */
    protected function curl($method, $url, $data = [], $header = [])
    {
        $method = strtoupper($method);
        $headerArray = array_merge($header, [
            'Expect:',
        ]);

        $ch = curl_init();
        switch ($method) {
            case 'GET': {
                $data and $url .= (stripos($url, '?') ? '&' : '?').http_build_query($data);
                break;
            }
            case 'FILE': {
                //就是带有文件的POST请求
                if (version_compare(PHP_VERSION,'5.5.0', '<')) {
                    throw new ApiException('php version too low');
                }
                foreach ($data as $key => $path) {
                    if (!is_file($path)) continue;
                    $data[$key] = curl_file_create($path);
                }
            }
            case 'POST': {
                curl_setopt($ch, CURLOPT_POST, 1);
                $data and curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
            case 'PUT': {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
            case 'DELETE':{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            }
            case 'JSON': {
                //注意PUT也有可能是JSON形式，所以最好是外面再封装一层，负责json_encode和加header
                is_array($data) and $data = json_encode($data);
                if (!is_string($data)) {
                    throw new ApiException('json error');
                }
                $headerArray = array_merge($headerArray, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, 1);
                $data and curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            }
            default: {
                $this->addError('method error', [
                    'method' => $method,
                ]);
                throw new ApiException('method error');
            }
        }
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headerArray,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FAILONERROR => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
        ]);

        $res = curl_exec($ch);
        $curl_error = curl_error($ch);

        if (!empty($curl_error) || $res === false) {
            $curl_info = curl_getinfo($ch);
            $msg = ($curl_error && is_string($curl_error)) ? $curl_error : 'curl error';
            $this->addError($msg, [
                'url' => $url,
                'data' => $data,
                'curl_error' => $curl_error,
                'curl_info' => $curl_info,
                'response' => $res,
            ]);

            curl_close($ch);
            throw new ApiException($msg, $curl_info['http_code']);

        } else {
            curl_close($ch);
            return $res;
        }
    }

}
