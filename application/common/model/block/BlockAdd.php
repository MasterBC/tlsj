<?php

namespace app\common\model\block;

use app\common\model\Common;
use think\Exception;
use think\facade\Log;
use think\facade\Request;

class BlockAdd extends Common
{
    public static $rechargeStatus = [
        1 => '待确认',
        3 => '已拒绝',
        9 => '已确认'
    ];

    protected $name = 'block_users_add';

    /**
     * 汇款充值
     * @param $userInfo
     * @return bool
     * @throws Exception
     */
    public function rechargeAdd($userInfo)
    {
        $num = (float)Request::param('num');
        $bid = (int)Request::param('bid');
        $img = (array)Request::param('img');
        if ($num <= 0) {
            throw new Exception('请输入充值数量');
        }
        if (empty($img)) {
            throw new Exception('请上传汇款截图');
        }
        try {
            $blockInfo = Block::getBlockInfoById($bid);
            if (empty($blockInfo)) {
                throw new Exception('非法操作');
            }

            $data = [
                'uid' => $userInfo['user_id'],
                'bid' => $blockInfo['id'],
                'web_address' => $blockInfo['address'],
                'add_time' => time(),
                'money_per' => 1,
                'add_money' => $num,
                'actual_money' => $num,
                'pay_time' => time(),
                'img' => implode(',', $img)
            ];

            self::create($data);

        } catch (\Exception $e) {
            Log::write('货币充值提交失败： ' . $e->getMessage(), 'error');
            throw new Exception('提交失败');
        }

        return true;
    }
}