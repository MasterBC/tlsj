<?php

namespace app\api\service;

use app\api\response\ReturnCode;
use app\common\model\Users;
use org\Jwt;
use think\facade\Cache;
use think\facade\Log;
use think\facade\Request;

class Token
{

    public $cacheDriver; // 缓存驱动

    public function __construct()
    {
        $this->cacheDriver = $this->getCacheDriver();
    }

    /**
     * 获取缓存驱动
     * @return \think\cache\Driver
     */
    public function getCacheDriver()
    {
        if (Container::has('cache_driver')) {
            $driver = Container::get('cache_driver');
        } else {
            try {
                $driver = Cache::store('redis');
            } catch (\Exception $e) {
                Log::write('redis连接失败: ' . $e->getMessage(), 'error');
                $driver = Cache::store('default');
            }
            Container::set('cache_driver', $driver);
        }


        return $driver;
    }

    /**
     * 生成token的键
     * @return string
     */
    private function getTokenKey(): string
    {
        $key = self::getKey();
        $next = true;
        while ($next) {
            if ($this->cacheDriver->has($key)) {
                $key = self::getKey();
            } else {
                $next = false;
            }
        }

        return $key;
    }

    /**
     * 生成键值
     * @return string
     */
    private function getKey(): string
    {
        $key = sha1(microtime(true) . uniqid() . get_rand_str(10)) . '-' . uniqid() . '-' . uniqid() . '-' . uniqid();
        $key = strtoupper($key);

        return $key;
    }

    /**
     * 生成token
     * @param $userInfo
     * @param int $expires
     * @return string
     */
    public function create($userInfo, $expires = 600)
    {
        $token = Jwt::getToken($userInfo, $expires);

        if ($token === false) {
            abort(404, 'token生成失败');
        }
        $key = self::getTokenKey();

        $this->cacheDriver->set($key, $token, $expires);

        return $key;
    }

    /**
     * 检查token
     * @return bool|mixed|object|\think\Response|\think\response\Json
     */
    public function check()
    {
        $token = Request::header('token');
        if ($token == '') {
            return ReturnCode::showReturnCode(1003);
        }

        if ($this->cacheDriver->has($token) === false) {
            return ReturnCode::showReturnCode(1004);
        }
        $userInfo = Jwt::decode($this->cacheDriver->get($token));
        if ($userInfo === false || empty($userInfo)) {
            return ReturnCode::showReturnCode(1004);
        }

        return true;
    }

    /**
     * 清除token
     * @return bool
     */
    public function clearToken()
    {
        try {
            $token = Request::header('token');

            // 删除token
            $this->cacheDriver->rm($token);

            return true;
        } catch (\Exception $e) {
            Log::write('后台退出清除token失败: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * @param bool $isUpdate 是否更新
     * @return array|bool|int|mixed|object|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo($isUpdate = false)
    {
        if ($isUpdate === true || $this->check() === true) {
            $token = Request::header('token');
            $userInfo = Jwt::decode($this->cacheDriver->get($token));

            $userInfo = json_decode(json_encode($userInfo), true);
            if ($isUpdate === true) {
                $userInfo = Users::where('user_id', $userInfo['user_id'])->find();
                if ($userInfo['frozen'] != 1) {
                    self::clearToken();
                    return 1104;
                }

                // 更新token时间
                $this->cacheDriver->set($token, Jwt::getToken($userInfo, 600), 600);
            }


            return $userInfo;
        }

        return false;
    }
}