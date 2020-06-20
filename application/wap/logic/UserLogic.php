<?php

namespace app\wap\logic;

use think\facade\Request;
use app\common\model\money\UsersMoney;
use app\common\model\UsersRedEnvelopeLog;
use app\common\server\bonus\Server;

class UserLogic
{
    /**
     * 领取红包
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-24T10:42:34+0800
     */
    public function receivingARedEnvelope($userInfo)
    {
        $type = Request::param('type', '', 'trim');
        if ($type == 'random') {
            $arr = explode('-', zf_cache('security_info.work_rand_arr_money'));
            $money = $arr[array_rand($arr)] ?? 0;
            //            $money = rand_float(0.1, floatval(zf_cache('security_info.random_total_hongbao_money')));
        } elseif ($type == 'working') {
            $money = rand_float(0.1, floatval(zf_cache('security_info.work_total_hongbao_money')));
        } elseif ($type == 'upgrade') {
            $redEnvelopeLogInfo = UsersRedEnvelopeLog::getUserPendingUpgradeRedEnvelopeInfo($userInfo['user_id']);
            if (empty($redEnvelopeLogInfo)) {
                return ['code' => -1, 'msg' => '没有待领取的红包'];
            }
            if ($redEnvelopeLogInfo['amount'] > 0) {
                (new UsersMoney())->amountChange($userInfo['user_id'], 3, $redEnvelopeLogInfo['amount'], 173, '升级红包');
            }
            $redEnvelopeLogInfo->status = 2;
            $redEnvelopeLogInfo->save();
            $bonusServer = new Server();
            $bonusServer->recommendedAward($redEnvelopeLogInfo['amount'], $userInfo,2);
            $bonusServer->clear();
            return ['code' => 1, 'msg' => '领取成功', 'data' => [
                'money' => $redEnvelopeLogInfo['amount']
            ]];
        } elseif ($type == 'sign') {
            $status = UsersRedEnvelopeLog::getUserTodaySignRedEnvelopeStatus($userInfo['user_id']);
            if ($status != 1) {
                return ['code' => -1, 'msg' => '今日已签到'];
            }
            $arr = explode('-', zf_cache('security_info.sign_rand_arr_money'));
            $money = $arr[array_rand($arr)] ?? 0;
        }
        $money = number_format($money, 2);

        if ($money > 0) {
            (new UsersMoney())->amountChange($userInfo['user_id'], 3, $money, 173, '领取红包');
            UsersRedEnvelopeLog::addLog($type, $userInfo['user_id'], $money, [], 2);

            return ['code' => 1, 'msg' => '领取成功', 'data' => [
                'money' => $money
            ]];
        } else {
            return ['code' => -1, 'msg' => '没有领到红包'];
        }
    }
}
