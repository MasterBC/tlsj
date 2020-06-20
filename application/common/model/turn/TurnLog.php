<?php

namespace app\common\model\turn;

use think\Model;
use think\facade\Request;

class TurnLog extends Model
{
    protected $name = 'turn_log';

    /**
     * 抽奖记录
     * @param $userId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTurnLog($userId)
    {
        $where['uid'] = $userId;
        $p = intval(Request::param('p'));
        $pSize = 10;
        return $this->where($where)->limit($p * $pSize, $pSize)->select();
    }
}