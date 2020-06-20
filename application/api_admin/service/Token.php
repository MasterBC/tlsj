<?php

namespace app\api_admin\service;

use app\api_admin\response\ReturnCode;
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
            return ReturnCode::showReturnCode(ReturnCode::LOGIN_CODE, 'token不能为空');
        }

        if ($this->cacheDriver->has($token) === false) {
            return ReturnCode::showReturnCode(ReturnCode::LOGIN_CODE, 'token验证失败');
        }
        $userInfo = Jwt::decode($this->cacheDriver->get($token));
        if ($userInfo === false || empty($userInfo)) {
            return ReturnCode::showReturnCode(ReturnCode::LOGIN_CODE, 'token验证失败');
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
     * 获取会员信息
     * @return bool|mixed|object
     */
    public function getUserInfo()
    {
        if (self::check() === true) {
            $token = Request::header('token');
            $userInfo = Jwt::decode($this->cacheDriver->get($token));

            $userInfo = json_decode(json_encode($userInfo), true);

            // 更新token时间
            $this->cacheDriver->set($token, Jwt::getToken($userInfo, 600), 600);

            return $userInfo;
        }

        return false;
    }
}