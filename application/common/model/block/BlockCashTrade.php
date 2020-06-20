<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Request;
use think\Db;

class BlockCashTrade extends Model
{
    protected $name = 'Block_cash_trade';

    /**
     * 根据条件查询最大值
     * @param $where
     * @param array $Field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMaxPrice($bid = '')
    {
        $where = [
            'bid' => (int)$bid,
        ];
        return $this->where($where)->max('actual_price');
    }

    /**
     * 根据条件查询最小值
     * @param $where
     * @param array $Field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMinPrice($bid = '')
    {
        $where = [
            'bid' => (int)$bid,
        ];
        return $this->where($where)->min('actual_price');
    }

    /**
     * 添加数据
     * @param BlockCashSell $sellInfo 挂卖信息
     * @param BlockCashBuy $buyInfo 挂买信息
     * @param int $blockId 货币id
     * @param float $num 交易数量
     * @param float $price 价格
     * @param string $fee 手续费
     * @param string $res 扣款id
     * @return int|string
     */
    public function addCashBlockTrade($sellInfo, $buyInfo, $blockId, $price, $num, $fee, $res)
    {
        $tradeData = [
            'sell_id' => $sellInfo['id']
            , 'sell_uid' => $sellInfo['uid']
            , 'buy_id' => $buyInfo['id']
            , 'buy_uid' => $buyInfo['uid']
            , 'bid' => $blockId
            , 'num' => $num
            , 'buy_price' => $sellInfo['price']
            , 'sell_price' => $sellInfo['price']
            , 'actual_price' => $price
            , 'fee' => $fee
            , 'fee_money' => $num * $fee / 100
            , 'money' => ($num - ($num * $fee / 100)) * $price
            , 'add_time' => time()
            , 'm_log_id' => $res
            , 'b_log_id' => $buyInfo['lock_id']
        ];
        $buyInfo->updateCashBuyStatus($num);
        $sellInfo->updateCashSellStatus($num);
        return $this->insertGetId($tradeData);
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
    public function getBlockCashTradeInfo($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $bid = Request::param('bid') > 0 ? Request::param('bid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = 10;
        switch ($type) {
            case 1:
                $where['buy_uid'] = $userId;
                $where['bid'] = $bid;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('num,money,actual_price,fee_money,add_time,sell_uid')->select();
                break;
            case 2:
                $where['sell_uid'] = $userId;
                $where['bid'] = $bid;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('num,money,actual_price,fee_money,add_time,buy_uid')->select();
                break;
        }

    }
}