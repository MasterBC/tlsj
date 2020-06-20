<?php

namespace app\common\model\branch;

use app\common\model\grade\Level;
use think\Db;
use think\facade\Log;
use app\common\model\Common;

class UsersBranchYj extends Common
{
    protected $name = 'users_branch_yj';

    /**
     * 根据接点id和位置获取业绩信息
     * @param $branchId
     * @param $pos
     * @return float
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getYjInfoByBranchIdAndPos($branchId, $pos)
    {
        return self::where('bid', $branchId)->where('type', $pos)->find();
    }

    /**
     * 统计总业绩
     * @param int $branchId 接点id
     * @param int $pos 接点位置
     * @return int|float 业绩
     */
    public static function countTotalYjByBranchId($branchId, $pos = 0)
    {
        if ($pos == 0) {
            return (float)self::where('bid', $branchId)->sum('total');
        } else {
            return (float)self::where('bid', $branchId)->where('type', $pos)->value('total');
        }
    }

    /**
     * 统计剩余业绩
     * @param int $branchId 接点id
     * @param int $pos 接点位置
     * @return int|float 业绩
     */
    public static function countNewYjByBranchId($branchId, $pos = 0)
    {
        if ($pos == 0) {
            return (float)self::where('bid', $branchId)->sum('new');
        } else {
            return (float)self::where('bid', $branchId)->where('type', $pos)->value('new');
        }
    }

    /**
     * 统计计算过的业绩
     * @param int $branchId 接点id
     * @param int $pos 接点位置
     * @return int|float 业绩
     */
    public static function countOutYjByBranchId($branchId, $pos = 0)
    {
        if ($pos == 0) {
            return (float)self::where('bid', $branchId)->sum('out');
        } else {
            return (float)self::where('bid', $branchId)->where('type', $pos)->value('out');
        }
    }
}