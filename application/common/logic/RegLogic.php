<?php

namespace app\common\logic;

use app\common\model\Users;
use app\common\model\block\Block;
use app\common\model\money\Money;
use app\common\model\money\UsersMoney;
use app\common\model\UsersData;
use app\common\model\grade\Level;
use think\facade\Request;
use app\facade\Password;
use think\facade\Log;
use think\Db;

class RegLogic
{

    /**
     * 注册处理
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doReg()
    {
        // 接收传值
        $tjrAccount = Request::param('tjrAccount');
        $same_phone_register_num = intval(zf_cache('reg_info.same_phone_register_num'));
        if ($same_phone_register_num > 1) {
            ##  一个手机号 可以注册多个会员
            $account = Request::param('account');
            $mobile = Request::param('mobile');
        } else {
            ##  一个手机号 只能注册一个会员号
            $account = Request::param('mobile');
            $mobile = Request::param('mobile');
        }
        $password = Request::param('password');
        $mobileCode = Request::param('mobileCode');
        $userModel = new Users();
        $tjrUserInfo = $userModel->getUserByAccount($tjrAccount, 2);
        if (!$tjrUserInfo) {
            exception('推荐码不存在');
        }
        $level = Request::param('level', '1', 'intval');
        $userInfo = $userModel->getUserByAccount($account);
        // 查出该账号是否存在
        if ($userInfo) {
            exception('会员账号已存在');
        }
        $userData = [];
        if (zf_cache('reg_info.register_phone_switch') === true) {
            if ($mobile == '') {
                exception('请输入手机号');
            }
            $mobileCount = $userModel->getUserCountByMobile($mobile);
            if (zf_cache('reg_info.same_phone_register_num') > 0 && $mobileCount >= zf_cache('reg_info.same_phone_register_num')) {
                exception('同手机号最多注册' . zf_cache('reg_info.same_phone_register_num') . '个账号');
            }
            // 判断注册时是否启用短信验证码
            if (zf_cache('reg_info.register_sms_switch') === true) {
                // 验证短信验证码
                $validateSmsCode = validate_sms_code_verify($mobile, $mobileCode, sid());
                if ($validateSmsCode['code'] != 1) {
                    exception($validateSmsCode['msg']);
                }
            }
        }
        Db::startTrans();
        try {
            $salt = Password::getPasswordSalt();
            $password = Password::getPassword($password, $salt);
            $user = [
                'account' => $account,
                'mobile' => $mobile,
                'first_tjr' => $tjrUserInfo['user_id'],
                'second_tjr' => $tjrUserInfo['first_tjr'],
                'third_tjr' => $tjrUserInfo['second_tjr'],
                'tjr_id' => $tjrUserInfo['user_id'],
                'password' => $password,
                'pass_salt' => $salt,
                'level' => $level,
                'reg_code' => $userModel->getInvitationCode(),
                'tjr_path' => $tjrUserInfo['tjr_path'] . ',' . $tjrUserInfo['user_id'],
                'reg_time' => time(),
                'activate' => zf_cache('reg_info.default_activate_state') == 1 ? 1 : 2,
                'jh_time' => zf_cache('reg_info.default_activate_state') == 1 ? time() : '',
                'video_num' => zf_cache('security_info.video_day_total_num'),
            ];
            $userInfo = $userModel->addUserDataInfo($user);
            // 如果没有推荐人 就将tjr_path改为用户自己的id
            if (intval($tjrUserInfo['user_id']) <= 0) {
                Users::where('user_id', $userInfo['user_id'])->update(['tjr_path' => $userInfo['user_id']]);
            }
            $this->afterReg($userInfo);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('注册失败' . $e->getMessage(), 'error');
            exception('注册失败');
        }
        return true;
    }

    /**
     * 注册后操作
     * @param Users $userInfo 会员信息
     * @return bool
     */
    private function afterReg($userInfo)
    {
        $moneyModel = new Money();
        $moneyModel->addUserMoney($userInfo['user_id']);
        $blockModel = new Block();
        $blockModel->addUserBlock($userInfo['user_id']);
        $reg_give_num = floatval(zf_cache('reg_info.reg_give_num'));
        $reg_give_mid = intval(zf_cache('reg_info.reg_give_mid'));
        if ($reg_give_num > 0 && $reg_give_mid > 0) {
            $userMoneyModel = new UsersMoney();
            $userMoneyModel->amountChange($userInfo['user_id'], $reg_give_mid, $reg_give_num, 100, '注册赠送', ['come_uid' => $userInfo['user_id']]);
        }
        return true;
    }

    /**
     * 管理员添加会员账号
     * @throws \Exception
     */
    public function adminAddUser()
    {
        $userModel = new Users();
        $userDataModel = new UsersData();
        $tjrAccount = Request::param('tjrAccount', '', 'trim');
        $password = Request::param('password');
        $account = Request::param('account', '', 'trim');
        $mobile = Request::param('mobile', '', 'trim');
        $email = Request::param('email', '', 'trim');

        $tjrInfo = $userModel->field('user_id,tjr_path,first_tjr,second_tjr')->getByAccount($tjrAccount);
        if (empty($tjrInfo)) {
            exception('推荐人不存在');
        }
        $userInfo = $userModel->getByAccount($account);
        if (!empty($userInfo)) {
            exception('此会员账号已存在');
        }
        $same_phone_register_num = intval(zf_cache('reg_info.same_phone_register_num'));
        if ($same_phone_register_num > 1) {
            ##  一个手机号 可以注册多个会员
            $account = Request::param('account');
            $mobile = Request::param('mobile');
        } else {
            ##  一个手机号 只能注册一个会员号
            $account = Request::param('mobile');
            $mobile = Request::param('mobile');
        }
        Db::startTrans();
        try {
            $salt = Password::getPasswordSalt();
            $password = Password::getPassword($password, $salt);
            $data = [
                'account' => $account,
                'mobile' => $mobile,
                'password' => $password,
                'reg_time' => time(),
                'tjr_id' => $tjrInfo['user_id'],
                'first_tjr' => $tjrInfo['user_id'],
                'second_tjr' => $tjrInfo['first_tjr'],
                'third_tjr' => $tjrInfo['second_tjr'],
                'pass_salt' => $salt,
                'level' => Level::where('status', '1')->max('level_id'),
                'reg_code' => $userModel->getInvitationCode(),
                'tjr_path' => $tjrInfo['tjr_path'] . ',' . $tjrInfo['user_id'],
                'activate' => zf_cache('reg_info.default_activate_state') == 1 ? 1 : 2,
                'jh_time' => zf_cache('reg_info.default_activate_state') == 1 ? time() : '',
                'video_num' => zf_cache('security_info.video_day_total_num'),
            ];
            $userInfo = $userModel->addUserDataInfo($data);
            $this->afterReg($userInfo);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('管理员添加会员账号失败: ' . $e->getMessage(), 'error');
            exception('添加失败');
        }
    }

}
