<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Request;
use think\helper\Time;

class BlockTransformLog extends Model
{
    protected $name = 'block_transform_log';

    /**
     * 获取每日的转换数量
     * @param int $userId
     * @param int $toBid
     * @return float|string
     */
    public function getUserBlockTransformDayTotal($userId, $toBid)
    {
        return $this->where('uid', $userId)->where('add_time', 'between', Time::today(), 'to_bid', $toBid)->sum('money');
    }

    /**
     * 钱包转账全部日志
     * @param int $userId 用户的id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBlockTransformLog($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p', 0, 'intval');
        $bid = Request::param('bid') > 0 ? Request::param('bid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = Request::param('size', 8, 'intval');
        $where['uid'] = $userId;
        $where['bid'] = $bid;
        return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,uid,bid,to_bid,money,add_time')->select();
    }

    /**
     * 添加货币转换日志
     * @param array $userInfo
     * @param int $blockId
     * @param int $toBlockId
     * @param int $outMoney
     * @param int $enterMoney
     * @param int $fee
     * @param int $poundage
     * @param string $note
     * @return int|string
     */
    public function addLog($userInfo, $blockId, $toBlockId, $outMoney, $enterMoney, $fee = 0, $poundage = 0, $note = '')
    {
        $data = [
            'uid' => $userInfo['user_id'],
            'bid' => $blockId,
            'to_bid' => $toBlockId,
            'money' => $outMoney,
            'add_time' => time(),
            'fee' => $fee,
            'fee_money' => $poundage,
            'to_money' => $enterMoney,
            'note' => $note
        ];

        return $this->insertGetId($data);
    }
}