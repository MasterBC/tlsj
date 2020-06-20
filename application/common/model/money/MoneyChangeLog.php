<?php

namespace app\common\model\money;

use think\Model;
use think\helper\Time;
use think\facade\Request;

class MoneyChangeLog extends Model
{
    protected $name = 'money_change_log';

    /**
     * 钱包转账全部日志
     * @param int $userId 用户的id
     * @param int $toUserId 收款方的id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyChangeLog($userId = 0, $toUserId = 0)
    {
        // 获取参数信息
        $p = Request::param('p', 0, 'intval');
        $mid = Request::param('mid') > 0 ? Request::param('mid') : 0;
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $userId = $userId > 0 ? $userId : false;
        $toUserId = $toUserId > 0 ? $toUserId : false;
        $pSize = Request::param('size', 8, 'intval');
        switch ($type) {
            case 0:
                $where['uid|to_uid'] = $userId;
                $where['mid'] = $mid;
                break;
            case 1:
                $where['uid'] = $userId;
                $where['mid'] = $mid;
                break;
            case 2:
                $where['to_uid'] = $toUserId;
                $where['mid'] = $mid;
                break;
        }

       return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,mid,to_mid,to_money,add_time,money,uid,to_uid')->select();
    }

    /**
     * 添加转账日志
     * @param array $userInfo 用户的信息
     * @param array $toUserInfo 对方账号的信息
     * @param int $MoneyId 货币id
     * @param int $toMoneyId 到账的钱包id
     * @param int|float $outMoney 转账金额
     * @param int|float $enterMoney 实际到账金额
     * @param int|float $fee 转账手续费百分比
     * @param int|float $poundage 转账手续费金额
     * @param string $note 备注
     * @return int|string
     */
    public function addLog($userInfo, $toUserInfo, $MoneyId, $toMoneyId, $outMoney, $enterMoney, $fee = 0, $poundage = 0, $note = '')
    {
        $data = [
            'uid' => $userInfo['user_id'],
            'to_uid' => $toUserInfo['user_id'],
            'mid' => $MoneyId,
            'to_mid' => $toMoneyId,
            'money' => $outMoney,
            'add_time' => time(),
            'fee' => $fee,
            'fee_money' => $poundage,
            'to_money' => $enterMoney,
            'note' => $note ? $note : $userInfo['account'] . '转账给' . $toUserInfo['account'] . '转账金额' . $outMoney . '实际到账' . $enterMoney
        ];

        return $this->insertGetId($data);
    }

    /**根据ID获取单条数据
     * @param int $id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyChangeLogInfoById($id = 0 )
    {
        $where = [
            'id'=> intval($id)
        ];
        return $this->where($where)->find();
    }

    /**
     * 获取每日的转账数量
     * @param $userId
     * @return float|string
     */
    public function getUserMoneyChangeDayTotal($userId,$mid)
    {
        return $this->where('uid', $userId)->where('mid',$mid)->where('add_time', 'between', Time::today())->sum('money');
    }

}