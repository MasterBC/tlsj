<?php
namespace app\common\model\money;

use think\Model;
use think\db\Where;
use think\facade\Request;
use think\Db;
class MoneyEpTrade extends Model
{
    protected $name = 'money_ep_trade';

    /**
     * 匹配订单
     * @param MoneyEpSell $sellInfo 挂卖信息
     * @param MoneyEpBuy $buyInfo 挂买信息
     * @param int $blockId 货币id
     * @param float $num 交易数量
     * @param float $price 价格
     * @param string $payInfo 收款信息
     * @param string $note 备注
     * @return int|string
     */
    public function addMoneyEpTrade($sellInfo, $buyInfo, $moenyId, $num, $price, $payInfo, $note = '')
    {
        $TradeData = [
            'sell_id' => $sellInfo['id'],
            'sell_uid' => $sellInfo['uid'],
            'buy_id' => $buyInfo['id'],
            'buy_uid' => $buyInfo['uid'],
            'mid' => $moenyId,
            'num' => $num,
            'buy_price'=>$sellInfo['price'],
            'sell_price'=>$sellInfo['price'],
            'actual_price' => $price,
            'money' => $num * $price,
            'pay_info' => $payInfo,
            'add_time' => time(),
            'order_sn' => time() . rand(111111, 999999),
            'note' => $note
        ];
        $buyInfo->updateEpMoneyBuyStatus($num);
        $sellInfo->updateEpMoneySellStatus($num);
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
    public function getMoneyEpTradeInfo($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $bid = Request::param('mid') > 0 ? Request::param('mid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = 10;
        switch ($type) {
            case 1:
                $where['buy_uid|sell_uid'] = $userId;
                $where['mid'] = $bid;
                $where['is_type'] = 1;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,sell_uid,is_type')->select();
                break;
            case 2:
                $where['sell_uid|buy_uid'] = $userId;
                $where['mid'] = $bid;
                $where['is_type'] = 2;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,buy_uid,is_type')->select();
                break;
        }

    }

    /**
     * 交易市场获取数据
     * @param int $userId
     * @param int
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyTradeInfo($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p', 0, 'intval');
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $bid = Request::param('mid') > 0 ? Request::param('mid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = Request::param('size', 8, 'intval');
        switch ($type) {
            case 1:
                $where['buy_uid|sell_uid'] = $userId;
                $where['mid'] = $bid;
                $where['is_type'] = ['in'=>1,2];
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,sell_uid,is_type,buy_uid')->select();
                break;
            case 2:
                $where['sell_uid|buy_uid'] = $userId;
                $where['mid'] = $bid;
                $where['is_type'] = 6;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('id,num,money,actual_price,add_time,buy_uid,is_type,sell_uid')->select();
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
    public function getTradeInfoById($id,$field = '')
    {
        $where = ['id'=>$id];
        if ($field !=''){
            return $this->where($where)->field($field)->find();
        }else{
            return $this->where($where)->find();
        }
    }
}