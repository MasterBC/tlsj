<?php

namespace app\common\model\money;

use think\facade\Request;
use think\helper\Time;
use think\Model;

class MoneyCarryBankLog extends Model
{
    protected $name = 'money_carry_bank_log';

    // 提现状态
    public static $status = [
        1 => '待审核',
        2 => '已审核',
        3 => '已拒绝',
        4 => '已撤销',
        9 => '已完成'
    ];

    /**
     * 添加提现日志
     * @param int $uid 会员id
     * @param int $mid 提现钱包
     * @param int $addMoney 提现金额
     * @param int $fee 汇率
     * @param int $feeMoney 手续费金额
     * @param int $outMoney 实际提现金额
     * @param int $openingId 银行卡类型id
     * @param string $bankAddress 分行分支
     * @param string $bankAccount 银行卡账号
     * @param string $bankName 持卡人姓名
     * @param int $status 状态
     * @param string $note 备注
     * @return int|string
     */
    public function addLog($uid, $mid, $addMoney, $fee, $feeMoney, $outMoney, $openingId, $bankAddress, $bankAccount, $bankName, $status, $note = '')
    {
        $data = [];
        $data['uid'] = isset($uid) ? $uid : '';
        $data['mid'] = isset($mid) ? $mid : '';
        $data['add_time'] = time();
        $data['add_money'] = isset($addMoney) ? $addMoney : '';
        $data['fee'] = isset($fee) ? $fee : '';
        $data['fee_money'] = isset($feeMoney) ? $feeMoney : '';
        $data['out_money'] = isset($outMoney) ? $outMoney : '';
        $data['opening_id'] = isset($openingId) ? $openingId : '';
        $data['bank_address'] = isset($bankAddress) ? $bankAddress : '';
        $data['bank_account'] = isset($bankAccount) ? $bankAccount : '';
        $data['bank_name'] = isset($bankName) ? $bankName : '';
        $data['status'] = $status;
        $data['note'] = $note;

        return $this->insertGetId($data);
    }

    /**
     * 获取提现日志
     * @param int $userId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyCarryLog($userId)
    {
        $p = Request::param('p');
        $status = Request::param('status');
        $p = $p ? $p : 0;
        $pSize = 10;
        $where = [];
        $where['uid'] = $userId;
        if($status > 0) {
            $where['status'] = $status;
        }

        return $this->where($where)
            ->limit($p * $pSize, $pSize)
            ->order('add_time desc')
            ->field('id,add_time,add_money,status,mid')
            ->select();
    }

    /**
     * 获取会员今日提现的金额
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @return float 会员今日提现的金额
     */
    public static function getTodayCarryAmountByUserId($userId, $moneyId)
    {
        return self::where('uid', $userId)
            ->where('mid', $moneyId)
            ->whereBetween('add_time', Time::today())
            ->whereIn('status', [1, 2, 9])
            ->sum('add_money');
    }


    /**
     * 获取提现配置
     *
     * @return array
     * @author gkdos
     * 2019-07-23 16:34:19
     */
    public static function getCarryConfig()
    {
        $data = [
             [
                'amount' => 1,
                'arrival' => 0.97,
                'num' => 1
            ],
             [
                'amount' => 3,
                'arrival' => 2.91,
                'num' => 1
            ],
             [
                'amount' => 5,
                'arrival' => 4.85,
                'num' => 1
            ],
            [
                'amount' => 20,
                'arrival' => 19.4,
                'num' => 0
            ],
            [
                'amount' => 30,
                'arrival' => 29.1,
                'num' => 0
            ],
            [
                'amount' => 40,
                'arrival' => 38.8,
                'num' => 0
            ],
            [
                'amount' => 50,
                'arrival' => 48.5,
                'num' => 0
            ],
            [
                'amount' => 100,
                'arrival' => 97,
                'num' => 0
            ],
        ];
        foreach ($data as $k => $v) {
            $data[$k]['id'] = $k;
        }

        return $data;
    }
}