<?php
namespace app\common\model\money;

use think\Model;
use think\db\Where;
use think\facade\Request;
use think\Db;

class MoneyCashSell extends Model
{
    protected $name = 'money_cash_sell';

    /**
     * 添加出售数据
     * @param int $blockId 货币id
     * @param int $userId 会员id
     * @param float $num 购买数量
     * @param float $price 价格
     * @param float $fee 手续费
     * @param int $logId 变动日志id
     * @return int|string
     */
    public function addMoneyCashSell($moenyId, $userId, $num, $price,$status = 1,$fee, $logId)
    {
        $sellData = [
            'mid' => $moenyId
            ,'uid' => $userId
            ,'add_time' => time()
            ,'num' => $num
            ,'price' => $price
            ,'fee' => $fee
            ,'fee_money' => $fee * $price * $num / 100
            ,'money' => ($num - ($num * $fee / 100)) * $price
            ,'status' => $status
            ,'stay_num' => ($num - ($num * $fee / 100))
            ,'log_id' => $logId
        ];
        if ($status == 9){
            $sellData['status'] = 9;
            $sellData['out_time'] = time();
        }
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
     * 根据id查询数据
     * @param $id
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCashSellInfoById($id)
    {
        $where = [
            'id'=>$id
        ];
        return $this->where($where)->find();
    }

    /**
     * 根据id修改数据
     * @param $id
     * @param $data
     */
    public function updateCashSellInfoById($id,$data=[])
    {
        $where = [
            'id' => intval($id)
        ];
        return $this->where($where)->update($data);
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
    public function getCashSellFieldById($uid,$field=[])
    {
        $where = new Where();
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        Request::param('mid') && $where['mid'] = Request::param('mid');
        $where['uid']=intval($uid);
        $pSize = 10;
        return $this->where($where)->field($field)->limit(($p * $pSize) . ',' . $pSize)->order('price desc')->select();
    }

    /**
     * 更新卖出信息状态
     * @param $num
     */
    public function updateCashMoneySellStatus($num)
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
}