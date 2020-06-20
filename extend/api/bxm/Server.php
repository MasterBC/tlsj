<?php

namespace api\bxm;

use Curl\Curl;
use org\AesSecurity;
use think\facade\Request;

class Server
{
    private $aesKey = 'bEIUfx2ZOKwkhxZV';

    /**
     * 获取视频广告
     *
     * @param int $userId 会员id
     * @param string $device 设备
     * @param string $imei imei
     * @return array
     * @author gkdos
     * 2019-09-20T12:42:16+0800
     */
    public function getInspireVideo($userId, $device, $imei)
    {
        $os = 1;
        if ($device == 'android') {
            $os = 1;
        } elseif ($device == 'ios') {
            $os = 2;
        } elseif ($device == 'other') {
            $os = 4;
        }
        $curl = new Curl();
        $curl->setHeaders([
            'Content-Type' => 'application/json;charset=utf-8'
        ]);
        $data = [
            'request_id' => md5(time() . rand(111111, 999999)),
            'position' => '70b12e47b4d84b1b87b088bb0f741add-4',
           // 'position' => '70b12e47b4d84b1b87b088bb0f741add-2',
            'app' => [
                'name' => 'test'
            ],
            'device' => [
                'ip' => get_ip(),
                'os' => $os,
                'imei' => $imei
            ],
            'ua' => Request::header('user-agent')
        ];
        $res = $curl->post('http://adsapi.fawulu.com/ticket/getInspireVideo?uid=' . $userId, $data);

        $res = json_decode(json_encode($res), true);

        return $res;
    }

    /**
     * 获取浮标广告
     *
     * @param int $userId 会员id
     * @return array
     * @author gkdos
     * 2019-09-23T17:24:04+0800
     */
    public function getBuoyAd($userId)
    {
        return [
            'returnValue' => [
                'redirectUrl' => 'https://i.iwanbei.cn/activities/?appKey=70b12e47b4d84b1b87b088bb0f741add&appEntrance=2&business=money&uid=' . $userId
            ]
        ];
        $curl = new Curl();
        $curl->setHeaders([
            'Content-Type' => 'application/json;charset=utf-8'
        ]);

        $data = [
            'adPositionId' => '70b12e47b4d84b1b87b088bb0f741add-2'
        ];

        $res = $curl->get('https://i.iwanbei.cn/activities/?appKey=70b12e47b4d84b1b87b088bb0f741add&appEntrance=2&business=money&uid=' . $userId, $data);

        dump($curl->getResponse());
        dump($curl->getResponseHeaders());
        $res = json_decode(json_encode($res), true);
        return $res;
    }

    /**
     * 解密
     *
     * @param string $data 要解密的数据
     * @return string
     * @author gkdos
     * 2019-09-26 16:25:24
     */
    private function decrypt($data)
    {
        return AesSecurity::decrypt($data, $this->aesKey);
    }

    /**
     * 获取回调数据
     *
     * @return array|mixed|string
     * @author gkdos
     * 2019-09-26 16:28:53
     */
    public function getNotifyData()
    {
        $data = $this->decrypt(Request::param('value'));

        if ($data === false) {
            return [];
        }

        $data = json_decode($data, true);

        return $data;
    }
}