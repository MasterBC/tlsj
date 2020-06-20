<?php

namespace app\common\model\block;

use think\Model;
use think\db\Where;
use think\facade\Request;

class BlockEpTrade extends Model
{
    protected $name = 'block_ep_trade';

    /**
     * 添加数据
     * @param BlockEpSell $sellInfo 挂卖信息
     * @param BlockEpBuy $buyInfo 挂买信息
     * @param int $blockId 货币id
     * @param float $num 交易数量
     * @param float $price 价格
     * @param string $payInfo 收款信息
     * @param string $note 备注
     * @return int|string
     */
    public function addBlockEpTrade($sellInfo, $buyInfo, $blockId, $num, $price, $payInfo, $note = '')
    {
        $TradeData = [
            'sell_id' => $sellInfo['id'],
            'sell_uid' => $sellInfo['uid'],
            'buy_id' => $buyInfo['id'],
            'buy_uid' => $buyInfo['uid'],
            'bid' => $blockId,
            'num' => $num,
            'buy_price' => $sellInfo['price'],
            'sell_price' => $sellInfo['price'],
            'actual_price' => $price,
            'money' => $num * $price,
            'pay_info' => $payInfo,
            'add_time' => time(),
            'order_sn' => time() . rand(111111, 999999),
            'note' => $note
        ];
        $buyInfo->updateBuyStatus($num);
        $sellInfo->updateSellStatus($num);
        return $this->insertGetId($TradeData);
    }


    /**
     * ajax获取数据
     * @param int $userId
     * @param int
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBlockEpTradeInfo($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $bid = Request::param('bid') > 0 ? Request::param('bid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = 10;
        switch ($type) {
            case 1:
                $where['buy_uid|sell_uid'] = $userId;
                $where['bid'] = $bid;
                $where['is_type'] = 1;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,sell_uid,is_type')->select();
                break;
            case 2:
                $where['sell_uid|buy_uid'] = $userId;
                $where['bid'] = $bid;
                $where['is_type'] = 2;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,buy_uid,is_type')->select();
                break;
        }

    }

    /**
     * 根据id获取数据
     * @param $id
     * @param string $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTradeInfoById($id, $field = '')
    {
        $where = ['id' => $id];
        if ($field != '') {
            return $this->where($where)->field($field)->find();
        } else {
            return $this->where($where)->find();
        }
    }

    /**
     * 根据id修改数据
     * @param $id
     * @param $data
     */
    public function updateEpSellInfoById($id, $data = [])
    {
        $where = [
            'id' => intval($id)
        ];
        return $this->where($where)->update($data);
    }
}