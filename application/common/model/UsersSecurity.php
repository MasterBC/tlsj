<?php

namespace app\common\model;

use think\Model;
use think\facade\Request;
use think\Db;

class UsersSecurity extends Model
{
    protected $name = 'users_security';

    /**
     * @param $post
     * @param $userId
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function doAddUserSecurityData($post, $userId)
    {
        $Id = Request::param('id');
        $p_one = Request::param('p_one');
        $d_one = Request::param('d_one');
        $p_two = Request::param('p_two');
        $d_two = Request::param('d_two');
        $p_three = Request::param('p_three');
        $d_three = Request::param('d_three');

        $data = [
            'uid' => $userId,
            'p_one' => $p_one,
            'd_one' => $d_one,
            'p_two' => $p_two,
            'd_two' => $d_two,
            'p_three' => $p_three,
            'd_three' => $d_three,
        ];

        // 有id传过来就是修改数据 否则就是添加数据
        if ($Id > 0) {
            $data['edit_time'] = time();
            $usersSecurity = Db::name('users_security')->where('uid', $userId)->update($data);
        } else {
            $data['add_time'] = time();
            $usersSecurity = Db::name('users_security')->insertGetId($data);
        }

        if ($usersSecurity > 0) {
            return true;
        } else {
            return false;
        }
    }
}
