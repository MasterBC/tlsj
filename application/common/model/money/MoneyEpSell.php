<?php
namespace app\common\model\money;

use think\Model;
use think\db\Where;
use think\facade\Request;
use think\Db;

class MoneyEpSell extends Model
{
    protected $name = 'money_ep_sell';

    /**
     * 添加出售数据
     * @param int $moneyId 钱包id
     * @param int $userId 会员id
     * @param float $num 购买数量
     * @param float $price 价格
     * @param float $fee 手续费
     * @param int $logId 变动日志id
     * @param string $userPayInfo
     * @return MoneyEpSell
     */
    public function addMoneyEpSell($moneyId, $userId, $num, $price, $fee, $logId, $userPayInfo = '')
    {
        //封装数据
        $sellData = [
            'mid' => $moneyId
            ,'uid' => $userId
            ,'add_time' => time()
            ,'num' => $num
            ,'price' => $price
            ,'fee' => $fee
            ,'fee_money' => $fee * $price * $num / 100
            ,'money' => ($num - ($num * $fee / 100)) * $price
            ,'status' => 1
            ,'stay_num' => ($num - ($num * $fee / 100))
            ,'log_id' => $logId
            ,'pay_info' => $userPayInfo
        ];
        return $this->create($sellData);
    }

    /**
     * ajax查询数据卖出
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSellList()
    {
        $where = new Where();
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        Request::param('mid') && $where['mid'] = Request::param('mid');
        $pSize = 10;
        $where['status'] = ['in','1,2'];
        return $this->where($where)->limit(($p * $pSize) . ',' . $pSize)->order('price desc')->select();
    }



    /**
     * Ajax获取数据
     * @param $uid 用户id
     * @param $bid 货币id
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEpSellFieldById($uid,$field=[])
    {
        $where = new Where();
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        Request::param('mid') && $where['mid'] = Request::param('mid');
        $where['uid']=intval($uid);
        $pSize = 10;
        return $this->where($where)->field($field)->limit(($p * $pSize) . ',' . $pSize)->order('price desc')->select();
    }

    /**
     * 根据id查询数据
     * @param $id
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEpSellInfoById($id)
    {
        $where = [
            'id'=>$id
        ];
        return $this->where($where)->find();
    }

    /**
     * 更新卖出信息状态
     * @param $num
     */
    public function updateEpMoneySellStatus($num)
    {

        if ($this->stay_num - $num <= 0) {
            $this->status = 9;
            $this->out_time = time();
        } else {
            $this->status = 2;
        }

        $this->stay_num = Db::raw('stay_num-' . $num);

        $this->save();
    }
    /**
     * 交易市场查询数据卖出
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTradeSellList()
    {
        $where = new Where();
        $p = Request::param('p', 0, 'intval');
        Request::param('mid') && $where['mid'] = Request::param('mid');
        Request::param('money') && $where['stay_num'] = Request::param('money');
        $pSize = Request::param('size', 8, 'intval');
        $where['status'] = 1;
        return  $this->where($where)->limit(($p * $pSize) . ',' . $pSize)->order('price desc')->select();
    }
}