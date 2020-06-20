<?php
declare (strict_types=1);

namespace app\common\behavior;

use app\common\model\money\UsersMoney;
use app\common\model\Users;
use think\Db;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Request;
use think\facade\Session;
use think\Response;

class AntiCcAttackStart
{
    /**
     * 添加访问记录
     */
    public function run()
    {
        /*$userModel = new Users();
        $userInfo = $userModel->getSessionUserInfo();

        $url = Request::url() . '_' . get_ip() . '_' . ($userInfo['user_id'] ?? '');
        if (strpos($url, '/verify') === false) {
            if (Cache::has($url)) {
                if (microtime(true) - Cache::get($url) <= 0.3) {
                    $note = date('Y-m-d H:i:s') . PHP_EOL;
                    $note .= 'ip: ' . get_ip() . PHP_EOL;
                    $note .= 'url:' . Request::url() . PHP_EOL;
                    $note .= 'post_info:' . json_encode(Request::post(), JSON_UNESCAPED_UNICODE) . PHP_EOL;
                    $note .= 'pet_info:' . json_encode(Request::get(), JSON_UNESCAPED_UNICODE) . PHP_EOL;
                    $note .= '---------------------------------------------------' . PHP_EOL;
                    file_put_contents(Env::get('RUNTIME_PATH') . 'log/访问过快.log', $note, FILE_APPEND);
                    die('请刷新重试');
                }
            }
        }
        Cache::set($url, microtime(true));*/
    }
}
