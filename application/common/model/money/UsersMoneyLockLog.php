<?php

namespace app\common\model\money;

use app\common\model\Common;
use app\common\model\Users;
use think\db\Where;
use think\facade\Request;


class UsersMoneyLockLog extends Common
{
    protected $name = 'users_money_lock';

    // 冻结释放状态
    public static $lockStatus = [
        '1' => '待释放',
        '2' => '释放中',
        '9' => '已释放'
    ];

    /**
     * 添加钱包冻结日志
     * @param int $userId 用户id
     * @param int $moneyId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @return int|string
     */
    public function addLog($userId, $moneyId, $money, $type, $note = '')
    {
        $userId = intval($userId);
        $moneyId = intval($moneyId);
        $money = floatval($money);
        $type = intval($type);
        $data = [
            'uid' => $userId,
            'mid' => $moneyId,
            'frozen_money' => $money,
            'stay_money' => $money,
            'type' => $type,
            'add_time' => time(),
            'lock_note' => $note
        ];

        return $this->insertGetId($data);
    }
}