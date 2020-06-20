<?php
namespace app\common\model\money;

use think\Model;
use think\facade\Request;

class MoneyCashTrade extends Model
{
    protected $name = 'money_cash_trade';

    /**
     * 添加数据
     * @param moneyCashSell $sellInfo 挂卖信息
     * @param moneyCashBuy $buyInfo 挂买信息
     * @param int $moneyId 钱包id
     * @param float $num 交易数量
     * @param float $price 价格
     * @param string $fee 手续费
     * @param string $res 钱包扣除自增id
     * @return int|string
     */
    public function addCashMoneyTrade($sellInfo,$buyInfo,$moneyId,$price,$num,$fee,$res)
    {
        $tradeData = [
            'sell_id'=>$sellInfo['id']
            ,'sell_uid'=>$sellInfo['uid']
            ,'buy_id'=>$buyInfo['id']
            ,'buy_uid'=>$buyInfo['uid']
            ,'mid'=>$moneyId
            ,'num'=>$num
            ,'actual_price'=>$sellInfo['price']
            ,'buy_price'=>$sellInfo['price']
            ,'sell_price'=>$sellInfo['price']
            ,'fee'=>$fee
            ,'fee_money'=>$num * $fee / 100
            ,'money'=>($num - ($num * $fee / 100))*$price
            ,'add_time'=>time()
            ,'m_log_id'=>$res
            ,'b_log_id'=>$sellInfo['log_id']
        ];
        $buyInfo->updateCashMoneyBuyStatus($num);
        $sellInfo->updateCashMoneySellStatus($num);
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
    public function getMoneyCashTradeInfo($userId = 0)
    {
        // 获取参数信息
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        $type = Request::param('type') > 0 ? Request::param('type') : 0;
        $mid = Request::param('mid') > 0 ? Request::param('mid') : 0;
        $userId = $userId > 0 ? $userId : false;
        $pSize = 10;
        switch ($type) {
            case 1:
                $where['buy_uid'] = $userId;
                $where['mid'] = $mid;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('num,money,actual_price,fee_money,add_time')->select();
                break;
            case 2:
                $where['sell_uid'] = $userId;
                $where['mid'] = $mid;
                return $this->where($where)->limit($p * $pSize, $pSize)->order('add_time desc')->field('num,money,actual_price,fee_money,add_time')->select();
                break;
        }

    }
}