<?php

namespace org;

use \Firebase\JWT\JWT as JwtService;

class Jwt
{
    // 密钥
    const SECRET_KEY = '938c2b7f35f6244cf573827e66608c2c';

    /**
     * 生成token
     * @param array $payload 载荷
     * @return string token
     */
    public static function encode($payload)
    {

        try {
            $jwt = JwtService::encode($payload, self::SECRET_KEY);

            return $jwt;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 解析token
     * @param string $token token
     * @return bool|object 成功后返回 解析后的值 失败返回错误信息
     */
    public static function decode($token)
    {
        try {
            $decoded = JwtService::decode($token, self::SECRET_KEY, array('HS256'));

            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 生成token
     * @param $userInfo
     * @param int $expires
     * @return string
     */
    public static function getToken($userInfo, $expires = 600)
    {
        $payload = $userInfo;
        $payload['exp'] = time() + $expires;

        $token = self::encode($payload);

        return $token;
    }
}