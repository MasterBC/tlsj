<?php

namespace app\common\model\block;

use think\Model;
use think\helper\Time;
use think\facade\Request;

class BlockChangeLog extends Model
{
    // 查增改删
    protected $name = 'block_change_log';

    /**
     * 获取会员每日的转账次数
     * @param $userId
     * @return float|string
     */
    public function getUserBlockChangeDayNum($userId)
    {
        return $this->where('uid', $userId)->where('add_time', 'between', Time::today())->count();
    }

    /**
     * 获取每日的转账数量
     * @param $userId
     * @return float|string
     */
    public function getUserBlockChangeDayTotal($userId)
    {
        return $this->where('uid', $userId)->where('add_time', 'between', Time::today())->sum('money');
    }

    /**
     * 添加转账日志
     * @param array $userInfo 用户的信息
     * @param array $toUserInfo 对方账号的信息
     * @param int $blockId 货币id
     * @param int $toBlockId 到账的货币id
     * @param int|float $outMoney 转账金额
     * @param int|float $enterMoney 实际到账金额
     * @param int|float $fee 转账手续费百分比
     * @param int|float $poundage 转账手续费金额
     * @param string $note 备注
     * @return int|string
     */
    public function addLog($userInfo, $toUserInfo, $blockId, $toBlockId, $outMoney, $enterMoney, $fee = 0, $poundage = 0, $note = '')
    {
        $data = [
            'uid' => $userInfo['user_id'],
            'to_uid' => $toUserInfo['user_id'],
            'bid' => $blockId,
            'to_bid' => $toBlockId,
            'money' => $outMoney,
            'add_time' => time(),
            'fee' => $fee,
            'fee_money' => $poundage,
            'to_money' => $enterMoney,
            'note' => $note ? $note : $userInfo['account'] . '转账给' . $toUserInfo['account'] . '转账金额' . $outMoney . '实际到账' . $enterMoney
        ];

        return $this->insertGetId($data);
    }

    /**
     * 货币转账日志
     * @param int $userId
     * @param int $toUserId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBlockChangeLog($userId = 0, $toUserId = 0)
    {
        // 获取参数信息
        $p = Request::param('p', 0, 'intval');
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $bid = Request::param('bid') > 0 ? Request::param('bid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $toUserId = $toUserId > 0 ? $toUserId : false;
        $pSize = Request::param('size', 8, 'intval');
        $where = [];
        switch ($type) {
            case 0:
                $where['uid|to_uid'] = $userId;
                $where['bid'] = $bid;
                break;
            case 1:
                $where['uid'] = $userId;
                $where['bid'] = $bid;
                break;
            case 2:
                $where['to_uid'] = $toUserId;
                $where['bid'] = $bid;
                break;
        }

        return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,add_time,bid,money,to_money,uid,to_uid')->select();
    }
}