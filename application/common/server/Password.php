<?php

namespace app\common\server;

use think\facade\Request;

class Password
{
    // 加密前缀
    protected $prefixKey;
    // 加密盐
    protected $saltString = 'abcdefghijklmnopqrstuvwxyz0123456789';

    public function __construct()
    {
        // 前缀是当前模块名
        $this->prefixKey = 'password_';
    }


    /**
     * 生成加密盐
     * @return string
     */
    public function getPasswordSalt()
    {
        $saltLen = mt_rand(4, min(30, strlen($this->saltString)-1));
        $string = "";
        for ($i = 1; $i <= $saltLen; $i++) {
            $string .= $this->saltString[rand(1,strlen($this->saltString)-1)];
        }
        return $string;
    }

    /**
     * 密码加密
     * @param string $password 输入的密码
     * @param string $passwordSalt 密码盐
     * @return string 加密后的密码
     */
    public function getPassword($password, $passwordSalt)
    {
        return md5(md5($this->prefixKey . $password) . $passwordSalt);
    }

    /**
     * 验证密码
     * @param string $password 输入的密码
     * @param array $entity 管理员信息
     * @return bool 验证结果
     */
    public function checkPassword($password, $entity)
    {
        return $this->getPassword($password, $entity->getPasswordSalt()) === $entity->getPassword();
    }

    /**
     * 验证二级密码
     * @param string $password 输入的密码
     * @param array $entity 管理员信息
     * @return bool 验证结果
     */
    public function checkPayPassword($password, $entity)
    {
        return $this->getPassword($password, $entity->getPasswordSalt()) === $entity->getPayPassword();
    }
}