<?php

namespace app\common\model\block;

use think\db\Where;
use think\facade\Request;
use think\Model;

class BlockLog extends Model
{
    protected $name = "block_log";

    /**
     * 获取会员货币变动记录
     * @param $userId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserBlockLogList($userId)
    {
        $where = new Where();
        $where['uid'] = (int)$userId;
        Request::param('type') && $where['is_type'] = Request::param('type', '', 'intval');
        Request::param('bid') && $where['bid'] = Request::param('bid', '', 'intval');
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
        $money = self::where($where->enclose())->limit($p * $pSize, $pSize)->order($sort_order)->select();
        return $money;
    }


    /**
     * 添加货币变动日志
     * @param int $userId 用户id
     * @param int $blockId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @param array $otherData 其他数据
     * [
     *      'admin_id' => '管理员id',
     *      'bonus_code' => '奖金记录code',
     *      'come_uid' => '来源会员id',
     *      'order_id' => '订单id'
     * ]
     * @return int|string
     */
    public function addLog($userId, $blockId, $money, $type, $note = '', $otherData = [])
    {
        return $this->insertGetId($this->generatingData($userId, $blockId, $money, $type, $note, $otherData));
    }

    /**
     * 生成货币变动日志的添加记录
     * @param int $userId 用户id
     * @param int $blockId 货币id
     * @param float $money 变动金额
     * @param int $type 类型
     * @param string $note 备注
     * @param array $otherData 其他数据
     * [
     *      'admin_id' => '管理员id',
     *      'bonus_code' => '奖金记录code',
     *      'come_uid' => '来源会员id',
     *      'order_id' => '订单id'
     * ]
     * @return array
     */
    public function generatingData($userId, $blockId, $money, $type, $note = '', $otherData = [])
    {
        $userId = intval($userId);
        $blockId = intval($blockId);
        $money = floatval($money);
        $type = intval($type);
        $data = [
            'uid' => $userId,
            'bid' => $blockId,
            'money' => $money,
            'is_type' => $type,
            'edit_time' => time(),
            'note' => $note
        ];
        if (isset($otherData['come_uid'])) {
            $data['come_uid'] = (int)$otherData['come_uid'];
        }
        if (isset($otherData['order_id'])) {
            $data['order_id'] = (int)$otherData['order_id'];
        }
        if (isset($otherData['bonus_code'])) {
            $data['bonus_code'] = (int)$otherData['bonus_code'];
        }
        if (isset($otherData['admin_id'])) {
            $data['admin_id'] = (int)$otherData['admin_id'];
        }
        // $data['total'] = UsersBlock::where('uid', $userId)->where('bid', $blockId)->value('money');

        return $data;
    }
}