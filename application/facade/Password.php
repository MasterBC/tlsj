<?php

namespace app\facade;

use think\Facade;

/**
 * @see \app\common\server\Password
 * @mixin \app\common\server\Password
 * @method string getPasswordSalt() 生成加密盐
 * @method string getPassword(string $password, string $passwordSalt) 获取密码密文
 * @method bool checkPassword(string $password, array $object) 验证密码
 */
class Password extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'app\common\server\Password';
    }
}