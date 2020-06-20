<?php

namespace app\common\model\money;

use think\db\Where;
use think\helper\Time;
use think\Model;
use think\facade\Request;

class MoneyTransformLog extends Model
{
    protected $name = 'money_transform_log';

    /**
     * 获取用户所有转出的金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:45:45
     */
    public static function getUserTotalMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('money');
    }

    /**
     * 获取用户所有转入的金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:45:45
     */
    public static function getUserTotalToMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('to_money');
    }

    /**
     * 获取会员今日转出的金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:48:31
     */
    public static function getUserTodayMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->whereBetween('add_time', Time::today())
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('money');
    }

    /**
     * 获取会员今日转入金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:50:34
     */
    public static function getUserTodayToMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->whereBetween('add_time', Time::today())
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('to_money');
    }

    /**
     * 获取会员昨日转出金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:51:56
     */
    public static function getUserYesterdayMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->whereBetween('add_time', Time::yesterday())
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('money');
    }

    /**
     * 获取会员昨日转入金额
     *
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $toMoneyId 目标钱包id
     * @return float
     * @author gkdos
     * 2019-07-11 11:53:00
     */
    public static function getUserYesterdayToMoney($userId, $moneyId, $toMoneyId)
    {
        return self::where('uid', $userId)
            ->whereBetween('add_time', Time::yesterday())
            ->where('mid', $moneyId)
            ->where('to_mid', $toMoneyId)
            ->sum('to_money');
    }

    /**
     * 根据id获取转换日志信息
     *
     * @param int $id 转换日志id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author gkdos
     * 2019-07-11 11:54:48
     */
    public static function getTransferLogInfoById($id)
    {
        return self::where('id', $id)->find();
    }

    /**
     * 钱包转账全部日志
     * @param int $userId 用户的id
     * @param int $toUserId 收款方的id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyTransformLog($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p', 0, 'intval');
        $mid = Request::param('mid') > 0 ? Request::param('mid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = Request::param('size', 8, 'intval');
        $where = [
            'uid' => $userId
            , 'mid' => intval($mid)
        ];
        return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,mid,to_mid,add_time,money,to_money,uid')->select();

    }

    /**
     * 添加日志
     * @param $userId 用户Id
     * @param $MoneyId 转出钱包Id
     * @param $toMoneyId 转入钱包Id
     * @param $outMoney 转换金额
     * @param $enterMoney 实际到账金额
     * @param int $fee 手续费比例
     * @param int $poundage 手续费实际金额
     * @param string $note 备注
     * @param string $account 转出钱包名称
     * @param string $toAccount 转入钱包名称
     * @return int|string
     */
    public function addLog($userId, $MoneyId, $toMoneyId, $outMoney, $enterMoney, $fee = 0, $poundage = 0, $note = '', $MoenyName = '', $toMoenyName = '')
    {
        $data = [
            'uid' => $userId,
            'mid' => $MoneyId,
            'money' => $outMoney,
            'to_mid' => $toMoneyId,
            'add_time' => time(),
            'fee' => $fee,
            'fee_money' => $poundage,
            'to_money' => $enterMoney,
            'note' => $note ? $note : $MoenyName . '转换给' . $toMoenyName . '转换金额' . $outMoney . '实际到账' . $enterMoney
        ];

        return $this->insertGetId($data);
    }
}