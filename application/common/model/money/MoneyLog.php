<?php

namespace app\common\model\money;

use app\common\model\Common;
use app\common\model\Users;
use think\db\Where;
use think\facade\Request;


class MoneyLog extends Common
{
    protected $name = 'money_log';

    /**
     * 钱包日志
     * @param $data 数据
     * @param $userInfo
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogMoney($userId)
    {
        $where = new Where;
        $where['uid'] = (int)$userId;
        Request::param('type') && $where['is_type'] = Request::param('type', '', 'intval');
        Request::param('mid') && $where['mid'] = Request::param('mid', '', 'intval');
        $where['note'] = ['like', '%' . Request::param('kwd') . '%'];
        $startTime = strtotime(Request::param('add_time'));
        $endTime = strtotime(Request::param('end_time'));
        if ($startTime && $endTime) {
            $where['edit_time'] = ['between', [$startTime, $endTime + 86400]];
        } elseif ($startTime > 0) {
            $where['edit_time'] = ['gt', $startTime];
        } elseif ($endTime > 0) {
            $where['edit_time'] = ['lt', $endTime];
        }
        $sort_order = (Request::param('order') ? Request::param('order') : 'id') . ' ' . (Request::param('sort') ? Request::param('sort') : 'desc');
        $p = Request::param('p', 0, 'intval');
        $pSize = Request::param('size', 8, 'intval');
        $money = $this->where($where->enclose())->limit($p * $pSize, $pSize)->order($sort_order)->select();
        return $money;
    }

        /**
     * 钱包日志
     * @param $data 数据
     * @param $userInfo
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMidLogMoney($userId,$mid)
    {
        $where = new Where;
        $where['uid'] = (int)$userId;
        $where['mid'] = (int)$mid;
        Request::param('type') && $where['is_type'] = Request::param('type', '', 'intval');
        $where['note'] = ['like', '%' . Request::param('kwd') . '%'];
        $startTime = strtotime(Request::param('add_time'));
        $endTime = strtotime(Request::param('end_time'));
        if ($startTime && $endTime) {
            $where['edit_time'] = ['between', [$startTime, $endTime + 86400]];
        } elseif ($startTime > 0) {
            $where['edit_time'] = ['gt', $startTime];
        } elseif ($endTime > 0) {
            $where['edit_time'] = ['lt', $endTime];
        }
        $sort_order = (Request::param('order') ? Request::param('order') : 'id') . ' ' . (Request::param('sort') ? Request::param('sort') : 'desc');
        $p = Request::param('p', 0, 'intval');
        $pSize = Request::param('size', 8, 'intval');
        $money = $this->where($where->enclose())->limit($p * $pSize, $pSize)->order($sort_order)->select();
        return $money;
    }
    
    
    
    /**
     * 根据ID查询用户钱包数据
     */
    public function getUserMoneyInfoById($uid)
    {
        $where = [
            'uid' => (int)$uid
        ];
        return $this->where($where)->find();
    }

    /**
     * 根据ID查询数据
     */
    public function getDataLisById($id)
    {
        $where = [
            'id' => (int)$id
        ];
        return $this->where($where)->find();
    }

    /**
     * 添加钱包变动日志
     * @param int $userId 用户id
     * @param int $moneyId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @param array $otherData 其他数据
     * [
     *      'admin_id' => '管理员id',
     *      'bonus_code' => '奖金code',
     *      'come_uid' => '来源会员id',
     *      'order_id' => '订单id'
     * ]
     * @return int|string
     */
    public function addLog($userId, $moneyId, $money, $type, $note = '', $otherData = [])
    {
        return $this->insertGetId($this->generatingData($userId, $moneyId, $money, $type, $note, $otherData));
    }

    /**
     * 生成钱包变动日志的添加记录
     * @param int $userId 用户id
     * @param int $moneyId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @param array $otherData 其他数据
     * [
     *      'admin_id' => '管理员id',
     *      'come_uid' => '来源会员id',
     *      'order_id' => '订单id',
     *      'bonus_code' => '奖金code'
     * ]
     * @return array
     */
    public function generatingData($userId, $moneyId, $money, $type, $note = '', $otherData = [])
    {
        $userId = intval($userId);
        $moneyId = intval($moneyId);
        $money = floatval($money);
        $type = intval($type);
        $data = [
            'uid' => $userId,
            'mid' => $moneyId,
            'money' => $money,
            'is_type' => $type,
            'edit_time' => time(),
            'note' => $note
        ];
        if (isset($otherData['come_uid'])) {
            $data['come_uid'] = (int) $otherData['come_uid'];
        }
        if (isset($otherData['order_id'])) {
            $data['order_id'] = (int) $otherData['order_id'];
        }
        if (isset($otherData['bonus_code'])) {
            $data['bonus_code'] = $otherData['bonus_code'];
        }
        if (isset($otherData['admin_id'])) {
            $data['admin_id'] = (int) $otherData['admin_id'];
        }
        $data['total'] = UsersMoney::where('uid', $userId)->where('mid', $moneyId)->value('money');
        return $data;
    }
}