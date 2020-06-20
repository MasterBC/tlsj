<?php

namespace app\common\logic;

use app\common\model\UserAuthName;
use think\facade\Log;
use think\facade\Request;
use think\facade\Validate;
use app\common\model\money\UsersMoney;
use app\common\model\money\MoneyLog;
use think\helper\Time;

class UserAuthNameLogic
{

    /**
     * @param $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addUserAuthNameData($userInfo)
    {
        $id = Request::param('id', '', 'intval');
        $username = Request::param('username', '', 'trim');
        if (!$username) {
            exception('请输入真实姓名');
        }

        $cardNumber = Request::param('card_number', '', 'trim');
        if (!$cardNumber) {
            exception('请输入身份证账号');
        }
        if (check_idcard($cardNumber) === false) {
            exception('请输入合法身份证账号');
        }

        $authNum = UserAuthName::where('card_number', $cardNumber)->count();
        if ($authNum > 0) {
            exception('此身份证己实名');
        }

//        $cardJust = Request::param('card_just', '', 'trim');
//        if (!$cardJust) {
//            exception('请输入身份证正面清晰照');
//        }
//
//        $cardBack = Request::param('card_back', '', 'trim');
//        if (!$cardBack) {
//            exception('请输入身份证反面清晰照');
//        }


        $info = UserAuthName::where(['id' => $id])->find();
        try {
            if ($id > 0) {
                $info->status = 1;
                $info->username = $username;
                $info->card_number = $cardNumber;
//                $info->card_just = $cardJust;
//                $info->card_back = $cardBack;
//                $info->hold_card = $cardBack;
                $info->save();
            } else {
                if (intval(zf_cache('security_info.auth_username_type')) == 9) {
                    (new UserAuthName())->addUserAuthName($userInfo['user_id'], $username, $cardNumber, '', '', '', 9);

                    $userAuthGiveNum = MoneyLog::where('uid', $userInfo['user_id'])->where('is_type', 150)->count();
                    if ($userAuthGiveNum <= 0) {
                        $authGiveMoney = zf_cache('security_info.auth_give_mid_num');
                        (new UsersMoney())->amountChange($userInfo['user_id'], 1, $authGiveMoney, 150, '实名认证', [
                            'come_uid' => $info['uid']
                        ]);
                    }

                    $tjrTotalConfigMoney = zf_cache('security_info.code_day_give_mid_num');
                    if ($tjrTotalConfigMoney > 0) {
                        $tjrTotalMoney = MoneyLog::where('uid', $userInfo['tjr_id'])->where('is_type', 151)->whereBetween('edit_time', Time::today())->sum('money');
                        if ($tjrTotalMoney < $tjrTotalConfigMoney) {
                            $authTjrGiveMoney = zf_cache('security_info.auth_give_mid_num');
                            (new UsersMoney())->amountChange($userInfo['tjr_id'], 1, $authTjrGiveMoney, 151, $userInfo['account'] . '实名认证', [
                                'come_uid' => $userInfo['user_id']
                            ]);
                        }
                    }
                } else {
                    (new UserAuthName())->addUserAuthName($userInfo['user_id'], $username, $cardNumber, '', '', '', 1);
                }
            }
        } catch (\Exception $e) {
            Log::write('提交实名失败' . $e->getMessage(), 'error');
            exception($e->getMessage());
        }
        return true;
    }

}
