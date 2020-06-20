<?php

namespace app\common\logic;

use app\facade\Password;
use think\facade\Request;
use app\common\model\money\Money;
use app\common\model\money\UsersMoneyAdd;

class RechargeLogic
{
    /**
     * 汇款充值
     * @param $userInfo
     * @return int|string
     * @throws \Exception
     */
    public function addMoney($userInfo)
    {
        $addMoney = Request::param('add_money', '', 'floatval'); if ($addMoney <= 0) exception('请输入充值金额');
        $img = Request::param('img');                                         if (empty($img)) exception('请上传汇款截图');
        $hkTime = Request::param('hk_time', '', 'trim');         if ($hkTime == '') exception('请选择汇款时间');
        $openingId = Request::param('opening_id', '', 'intval'); if ($openingId <= 0) exception('请选择汇款银行');
        $mid = Request::param('mid', '', 'intval');              if ($mid <= 0) exception('请选择充值钱包');
        $secpwd = Request::param('secpwd', '', 'intval');        if (!$secpwd) exception('请输入二级密码');

        // 判断二级密码是否正确
        if (!Password::checkPayPassword($secpwd, $userInfo)) {
            exception('二级密码错误');
        }

        $moneyPer = (new Money)->getMoneyInfoById($mid)['c_pre'];
        $actualMoney = $addMoney * $moneyPer;

        $res = UsersMoneyAdd::addLog($userInfo['user_id'], $mid, $openingId, $addMoney, $moneyPer, $actualMoney, strtotime($hkTime), $img);
        return $res;
    }
}