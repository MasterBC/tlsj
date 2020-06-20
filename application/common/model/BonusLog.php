<?php

namespace app\common\model;

use think\Db;
use think\db\Where;
use think\helper\Time;
use think\Model;
use think\facade\Request;

class BonusLog extends Model
{
    protected $name = 'bonus_log';

    /**
     * 奖金日志数据
     * @param $data 查询数据
     * @param $userInfo 用户信息
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogBonus($userId = '')
    {
        $where = new Where;
        $where['uid'] = (int)$userId;
        Request::param('type') && $where['bonus_id'] = Request::param('type');
        $startTime = strtotime(Request::param('add_time'));
        $endTime = strtotime(Request::param('end_time'));
        if ($startTime && $endTime) {
            $where['add_time'] = ['between', [$startTime, $endTime + 86400]];
        } elseif ($startTime > 0) {
            $where['add_time'] = ['gt', $startTime];
        } elseif ($endTime > 0) {
            $where['out_time'] = ['lt', $endTime];
        }
        $sort_order = (Request::param('order') ? Request::param('order') : 'id') . ' ' . (Request::param('sort') ? Request::param('sort') : 'desc');
        $p = Request::param('p', 0, 'intval');
        $pSize = Request::param('size', 8, 'intval');
        $bonusInfo = $this->where($where->enclose())->limit($p * $pSize, $pSize)->order($sort_order)->select();
        return $bonusInfo;
    }

    /**
     * 添加奖金记录
     * @param int $bonusId 奖金id
     * @param int $userId 拿奖会员id
     * @param int $comeUserId 来源会员id
     * @param float $comeMoney 计算金额
     * @param float $money 奖金金额
     * @param int $layer 层数 or 代数
     * @return int|string
     */
    public function addLog($bonusId, $userId, $comeUserId, $comeMoney, $money, $layer = 1)
    {
        return $this->create($this->generatingData($bonusId, $userId, $comeUserId, $comeMoney, $money, $layer));
    }

    /**
     * 生成奖金记录
     * @param int $bonusId 奖金id
     * @param int $userId 拿奖会员id
     * @param int $comeUserId 来源会员id
     * @param float $comeMoney 计算金额
     * @param float $money 奖金金额
     * @param int $layer 层数 or 代数
     * @return array
     */
    public function generatingData($bonusId, $userId, $comeUserId, $comeMoney, $money, $layer = 1)
    {
        $data = [
            'bonus_id' => (int)$bonusId,
            'uid' => (int)$userId,
            'come_uid' => (int)$comeUserId,
            'come_money' => $comeMoney,
            'stay_money' => $money,
            'money' => $money,
            'layer' => (int)$layer,
            'add_time' => time(),
            'code' => str_replace('.', '', uniqid($bonusId . '_' . $userId, true)),
            'out_time' => 0
        ];

        return $data;
    }

    /**
     * 根据id 查询数据
     * @param $id
     */
    public static function getBonusById($id)
    {
        $where = [
            'id' => (int)$id
        ];
        return self::where($where)->find();
    }


    /**
     * 根据id 查询会员的奖金数据
     * @param $uid会员id
     */
    public function getUserBonusInfoById($uid)
    {
        $where = [
            'uid' => (int)$uid
        ];
        return $this->where($where)->find();
    }

    /**
     * 获取会员今日奖金
     * @param int $userId 会员id
     * @return float
     */
    public static function getToDayBonusByUid($userId)
    {
        return self::where('uid', (int)$userId)->whereBetween('add_time', Time::today())->sum(Db::raw('money-stay_money'));
    }

    /**
     * 获取会员奖金总和
     * @param int $uid 会员id
     * @return float
     */
    public static function getBonusSumByUid($uid): float
    {
        return (float)self::where('uid', (int)$uid)->sum('money');
    }

    /**
     * 根据会员id查询会员今日静态奖金
     * @param $userId
     * @return float
     */
    public static function getUserTodayStaticBonusByUid($userId)
    {
        return self::where('uid', (int)$userId)->where('bonus_id', 3)->whereBetween('add_time', Time::today())->sum(Db::raw('money-stay_money'));
    }

    /**
     * 根据会员id查询会员今日动态奖金
     * @param $userId
     * @return float
     */
    public static function getUserTodayDynamicBonusByUid($userId)
    {
        return self::where('uid', (int)$userId)->whereIn('bonus_id', [1, 2, 4])->whereBetween('add_time', Time::today())->sum(Db::raw('money-stay_money'));
    }

}