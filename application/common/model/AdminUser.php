<?php

namespace app\common\model;

use think\facade\Session;

class AdminUser extends Common
{
    protected $name = 'admin_user';

    const SESSION_NAME = 'mysite_admin';

    /**
     * 根据账号获取管理员信息
     * @param $username
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByUsername($username)
    {

        $where = [
            'user_name' => $username
        ];

        return $this->where($where)->find();
    }

    /**
     * 根据id获取管理员信息
     * @param int $id
     * @return Common|array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserById($id)
    {
        $where = [
            'admin_id' => intval($id)
        ];

        return $this->where($where)->find();
    }

    /**
     * 获取session
     */
    public function getAdminUserInfo()
    {
        if (Session::has(self::SESSION_NAME)) {
            return Session::get(self::SESSION_NAME);
        }
        return [];
    }

    /**
     * 获取管理员id
     */
    public function getAdminUserId()
    {
        $session = $this->getAdminUserInfo();
        return $session['admin_id'] ?? 0;
    }

    /**
     * 获取管理员的密码盐
     * @return mixed
     */
    public function getPasswordSalt()
    {
        return $this->pass_salt;
    }

    /**
     * 获取管理员的密码
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * 获取管理员的id
     * @return mixed
     */
    public function getId()
    {
        return $this->admin_id;
    }
}
