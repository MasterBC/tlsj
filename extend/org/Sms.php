<?php

namespace org;

use \Curl\Curl;

class Sms
{

    var $url;
    private $config;
    private $mobile;
    private $content;
    private $err = ''; // 错误内容

    /**
     * Sms constructor.
     */
    public function __construct()
    {
        $this->config = zf_cache('sms_info.');

        $this->url = 'http://utf8.api.smschinese.cn/';
    }

    /**
     * 设置发送手机号
     * @param $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * 设置发送内容
     * @param $text
     */
    public function setContent($text)
    {
        $this->content = $text;
    }

    /**
     * 发送短信
     */
    public function sendSms()
    {
        try {
            $data = [
                'Uid' => $this->config['sms_user'],
                'Key' => $this->config['sms_key'],
                'smsMob' => $this->mobile,
                'smsText' => $this->content
            ];
            $curl = new Curl();
            $res = $curl->post($this->url, $data);
            $checkRes = $this->checkSend($res); // 检查短信
            if ($checkRes) {
                return 0;
            }
            return $res;
        } catch (\Exception $e) {
            $this->err = '发送失败';
            return 0;
        }
    }

    /**
     * 获取短信数量
     */
    public function getSmsNum()
    {
        try {
            $data = [
                'Action' => 'SMS_Num',
                'Uid' => $this->config['sms_user'],
                'Key' => $this->config['sms_key'],
            ];
            $curl = new Curl();
            $res = $curl->post('http://www.smschinese.cn/web_api/SMS/', $data);
            return $res;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 检查短信发送
     * @param string $res 发送返回信息
     * @return string 错误信息
     */
    public function checkSend($res)
    {
        $this->err = '';
        if ($res < 1) {
            $this->err .= '发送失败';
        }
        switch ($res) {
            case -4:
                $this->err .= '手机号错误!';
                break;
            case -14:
                $this->err .= '短信内容含有非法字符!';
                break;
            case -6:
                $this->err .= 'IP限制!';
                break;
        }
        return $this->err;
    }

    /**
     * 返回错误信息
     */
    public function showErr()
    {
        return $this->err;
    }

}
