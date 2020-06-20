<?php

namespace app\common\model;

use think\Model;
use think\facade\Request;

class UsersLog extends Model
{
    protected $name = 'users_action';

    /**
     * 添加会员动态日志
     * @param $userId
     * @param $type
     * @param string $note
     * @return int|string
     */
    public function addLog($userId, $type, $note = '')
    {
        $data = [
            'uid' => $userId,
            'type' => $type,
            'note' => $note,
            'add_time' => time(),
            'log_ip' => get_ip(),
            'log_url' => Request::url(),
            'equipment' => equipment_system()
        ];

        return $this->insertGetId($data);
    }


}
