<?php
declare (strict_types=1);

namespace app\common\behavior;

use think\facade\Request;
use think\facade\Cache;
use think\Response;

class AntiCcAttack
{
    /**
     * 添加访问记录
     */
    public function run()
    {
        //代理IP直接退出
        empty($_SERVER['HTTP_VIA']) or exit('Access Denied');

        $cur_time = time();
        $seconds = '60'; //时间段[秒]
        $refresh = '15'; //刷新次数
        $ip = get_ip();
        if (Cache::has($ip . 'last_time')) {
            Cache::set($ip . 'refresh_times', Cache::get($ip . 'refresh_times') + 1);
        } else {
            Cache::set($ip . 'refresh_times', 1);
            Cache::set($ip . 'last_time', $cur_time);
        }

        //处理监控结果
        if ($cur_time - Cache::get($ip . 'last_time') < $seconds) {
            if (Cache::get($ip . 'refresh_times') >= $refresh) {
                file_put_contents(__DIR__ . '/cc.log', date('Y-m-d H:i:s') . '————' . $ip . '————' . $_SERVER['REQUEST_URI'] . PHP_EOL, FILE_APPEND);
                //跳转至攻击者服务器地址
                header(sprintf('Location:%s', 'http://' . $ip));
                exit('请求频率太快，稍候' . $seconds . '秒后再访问！');
            }
        } else {
            Cache::set($ip . 'refresh_times', 0);
            Cache::set($ip . 'last_time', $cur_time);
        }
    }
}
