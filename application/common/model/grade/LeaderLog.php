<?php

namespace app\common\model\grade;

use app\common\model\Common;

class LeaderLog extends Common
{
    protected $name = 'leader_log';

    /**
     * 会员等级升级日志
     * @param int $userId 用户id
     * @param int $frontId 升级前id
     * @param int $newId 升级后id
     * @param int $status 待审核 1
     * @param string $note 升级日志
     * @return int|string
     */
    public static function addLog($userId, $frontId, $newId, $status = 1, $note = '')
    {
        $data = [
            'uid' => $userId,
            'front_id' => $frontId,
            'new_id' => $newId,
            'add_time' => time(),
            'status' => $status,
            'note' => $note
        ];
        return self::insertGetId($data);
    }
}