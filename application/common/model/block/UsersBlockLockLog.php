<?php

namespace app\common\model\block;

use app\common\model\Bonus;
use app\common\model\Common;
use app\common\model\Users;
use app\common\model\money\UsersMoney;
use think\db\Where;
use think\facade\Request;


class UsersBlockLockLog extends Common
{
    protected $name = 'block_users_lock';

    // 冻结状态
    public static $lockStatus = [
        '1' => '待释放',
        '2' => '释放中',
        '9' => '已释放'
    ];

    /**
     * 获取货币冻结类型
     * @param int $type
     * @return string
     */
    public static function getLogType($type = 0)
    {
        $data = Bonus::getBonusNames();
        $data[101] = '管理操作';
        $data[0] = $data;

        return $data[intval($type)] ?? '';
    }

    /**
     * 添加钱包冻结日志
     * @param int $userId 用户id
     * @param int $blockId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @return int|string
     */
    public function addLog($userId, $blockId, $money, $type, $note = '')
    {
        $userId = intval($userId);
        $blockId = intval($blockId);
        $money = floatval($money);
        $type = intval($type);
        $data = [
            'uid' => $userId,
            'bid' => $blockId,
            'frozen_money' => $money,
            'stay_money' => $money,
            'type' => $type,
            'add_time' => time(),
            'lock_note' => $note
        ];

        return $this->insertGetId($data);
    }
}