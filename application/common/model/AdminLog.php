<?php

namespace app\common\model;

use think\Model;
use think\facade\Request;

class AdminLog extends Model
{
    protected $name = 'admin_log';

    /**
     * 添加管理员日志
     * @param string $note
     * @param int $adminId
     * @return int|string
     */
    public static function addLog($note, $otherData = [], $adminId = 0)
    {
        $userModel = new AdminUser();
        $data = [
            'admin_id' => $adminId ? $adminId : $userModel->getAdminUserId(),
            'note' => $note,
            'other_data' => serialize($otherData),
            'add_time' => time(),
            'log_ip' => get_ip(),
            'log_url' => Request::url(),
            'equipment' => equipment_system()
        ];

        return self::insertGetId($data);
    }


}
