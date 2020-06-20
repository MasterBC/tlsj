<?php

namespace app\common\model\money;

use app\common\model\AdminUser;
use app\common\model\Common;
use think\facade\Request;

class UsersMoneyAdd extends Common
{
    protected $name = "users_money_add";

    // 状态
    public static $status = [
        '1' => '待审核',
        '3' => '已拒绝',
        '9' => '已审核'
    ];

    /**
     * 添加充值记录
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param int $bankId 银行id
     * @param float $addMoney 充值金额
     * @param float $moneyPer 汇率
     * @param float $actualMoney 到账金额
     * @param string $payTime 支付时间
     * @param array|string $img 汇款截图
     * @return int|string
     */
    public static function addLog($userId, $moneyId, $bankId, $addMoney, $moneyPer, $actualMoney, $payTime, $img)
    {
        $data = [
            'uid' => $userId,
            'bank_id' => $bankId,
            'mid' => $moneyId,
            'add_money' => $addMoney,
            'money_per' => $moneyPer,
            'actual_money' => $actualMoney,
            'pay_time' => $payTime,
            'img' => is_array($img) ? implode(',', $img) : $img,
            'status' => 1
        ];

        return self::insertGetId($data);

    }

    /**
     * 确认会员充值
     * @return bool
     * @throws \Exception
     */
    public function affirmReview()
    {
        $this->status = 9;
        $this->affirm_time = time();
        $this->admin_id = (new AdminUser())->getAdminUserId();

        $this->save();

        if ($this->actual_money > 0) {
            (new UsersMoney())->amountChange($this->uid, $this->mid, $this->actual_money, 107, '汇款充值', ['admin_id' => $this->admin_id]);
        }

        return true;
    }

    /**
     * 拒绝会员充值
     * @param string $refuseContent 拒绝理由
     * @return bool
     * @throws \Exception
     */
    public function refuseReview($refuseContent = '')
    {
        $this->status = 3;
        $this->refuse = $refuseContent;
        $this->refuse_time = time();
        $this->admin_id = (new AdminUser())->getAdminUserId();

        $this->save();

        return true;
    }

    /**
     * 获取充值日志
     * @param int $userId
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyAddLog($userId)
    {
        $p = Request::param('p');
        $p = $p ? $p : 0;
        $pSize = 10;
        $where = [];
        $where['uid'] = $userId;

        return $this->where($where)->order('pay_time desc')->limit($p * $pSize, $pSize)->field('id,pay_time,add_money,status')->select();
    }
}