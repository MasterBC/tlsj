<?php

namespace app\common\server\kuaidi100;

use Curl\Curl;

class Server
{
    const KEY = 'LwZhIFKt8865';

    public static $logisticsName = [
        'EMS' => 'ems',
        '中国邮政' => 'ems',
        '申通快递' => 'shentong',
        '百世快递' => 'htky',
        '京东快递' => 'jingdong',
        '圆通速递' => 'yuantong',
        '顺丰速运' => 'shunfeng',
        '天天快递' => 'tiantian',
        '韵达快递' => 'yunda',
        '中通速递' => 'zhongtong',
        '龙邦物流' => 'longbanwuliu',
        '宅急送' => 'zhaijisong',
        '全一快递' => 'quanyikuaidi',
        '汇通速递' => 'huitongkuaidi',
        '民航快递' => 'minghangkuaidi',
        '亚风速递' => 'yafengsudi',
        '快捷速递' => 'kuaijiesudi',
        '华宇物流' => 'tiandihuayu',
        '中铁快运' => 'zhongtiewuliu',
        'FedEx' => 'fedex',
        'UPS' => 'ups',
        'DHL' => 'dhl'
    ];

    /**
     * 提交订阅
     * @param $data
     */
    public function pollRequest($data)
    {

        $post_data = array();
        $post_data["schema"] = 'json' ;

        //callbackurl请参考callback.php实现，key经常会变，请与快递100联系获取最新key
        $post_data["param"]='{"company":"yunda", "number":"3714450223694","from":"广东深圳", "to":"北京朝阳",';
        $post_data["param"]=$post_data["param"].'"key":"LwZhIFKt8865",';
        $post_data["param"]=$post_data["param"].'"parameters":{"callbackurl":"http://www.yourdmain.com/kuaidi"}}';

        $url='http://www.kuaidi100.com/poll';

        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }

        $post_data=substr($o,0,-1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        dump($result);
    }

    /**
     * 获取快递编码
     * @param $shippingName
     * @return mixed|string
     */
    public function getCompany($shippingName)
    {
        foreach (self::$logisticsName as $k => $v) {
            if (count(explode($k, $shippingName)) > 1) {
                return $v;
            }
        }

        return '';
    }
}