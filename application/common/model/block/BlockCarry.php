<?php

namespace app\common\model\block;

use app\common\model\Common;
use think\Db;
use think\Exception;
use think\facade\Log;
use think\facade\Request;

class BlockCarry extends Common
{
    public static $carryStatus = [
        1 => '待审核',
        2 => '已审核',
        3 => '已拒绝',
        4 => '已撤销',
        9 => '已完成'
    ];

    protected $name = 'block_carry_log';

    /**
     * 申请提现
     * @param $userInfo
     * @return bool
     * @throws Exception
     */
    public function applyCarry($userInfo)
    {
        $num = (float)Request::param('num');
        $bid = (int)Request::param('bid');
        $address = Request::param('address', '', 'trim');
        if ($address == '') {
            throw new Exception('请输入提现地址');
        }
        if ($num <= 0) {
            throw new Exception('请输入提现数量');
        }
        if ($bid <= 0) {
            throw new Exception('请选择提现类型');
        }
        $blockInfo = Block::getBlockInfoById($bid);
        if (empty($blockInfo)) {
            throw new Exception('非法操作');
        }
        $userBlockModel = new UsersBlock();
        $balance = $userBlockModel->getAmountByUid($userInfo['user_id'], $bid);
        if ($balance < $num) {
            throw new Exception($blockInfo['name_cn'] . '不足');
        }
        Db::startTrans();
        try {
            $userBlockModel->amountChange($userInfo['user_id'], $blockInfo['id'], '-' . $num, 110, '申请提现');
            $data = [
                'uid' => $userInfo['user_id'],
                'bid' => $blockInfo['id'],
                'web_address' => $blockInfo['address'],
                'user_address' => $address,
                'add_time' => time(),
                'add_num' => $num,
                'out_num' => $num,
            ];

            self::create($data);

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            Log::write('申请提现提交失败： ' . $e->getMessage(), 'error');
            throw new Exception('提交失败');
        }

        return true;
    }

    /**
     * 根据id获取提现信息
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCarryInfoById($id)
    {
        return self::where('id', $id)->find();
    }
}