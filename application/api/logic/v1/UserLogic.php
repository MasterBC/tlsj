<?php

namespace app\api\logic\v1;

use app\api\response\ReturnCode;
use app\common\model\block\Block;
use app\common\model\money\Money;
use app\common\model\Users;
use app\common\model\UsersData;
use app\common\model\UsersLog;
use app\facade\Password;
use think\facade\Log;
use think\facade\Request;

class UserLogic
{

    /**
     * 会员登录
     * @return array
     */
    public function doLogin()
    {
        try {
            $userModel = new Users();
            $userLogModel = new UsersLog();
            $account = Request::param('account', '', 'trim');
            $password = Request::param('password', '', 'trim');
            if ($account == '' || $password == '') {
                return ['code' => 1005];
            }

            $userInfo = $userModel->getUserByAccount($account);
            if (empty($userInfo)) {
                return ['code' => 1101];
            }
            if ($userInfo['frozen'] != 1) {
                return ['code' => 1104];
            }
            if (!Password::checkPassword($password, $userInfo)) {
                $userLogModel->addLog($userInfo['user_id'], 1, '登陆密码错误 密码: ' . $password);
                return ['code' => 1102];
            }
            $userLogModel->addLog($userInfo['user_id'], 1, '登陆成功');

            return ['code' => ReturnCode::SUCCESS_CODE, 'user' => $userInfo];
        } catch (\Exception $e) {
            Log::write('会员登录失败: ' . $e->getMessage(), 'error');
            return ['code' => ReturnCode::ERROR_CODE];
        }
    }

    /**
     * 会员注册
     * @return array
     */
    public function doReg()
    {
        try {
            $userModel = new Users();
            $regCode = Request::param('reg_code', '', 'trim');
            $account = Request::param('account', '', 'trim');
            $password = Request::param('password', '', 'trim');
            if ($regCode == '' || $account == '' || $password == '') {
                return ['code' => 1005];
            }

            $tjrInfo = $userModel->getUserByAccount($regCode, 2);
            if (!$tjrInfo) {
                return ['code' => 1301];
            }

            $userInfo = $userModel->getUserByAccount($account);
            if ($userInfo) {
                return ['code' => 1302];
            }
            $userData = [];
            $userDataModel = new UsersData();
            $userDataId = $userDataModel->addUserDataInfo($userData);
            $salt = Password::getPasswordSalt();
            $password = Password::getPassword($password, $salt);
            $user = [
                'account' => $account,
                'tjr_id' => $tjrInfo['user_id'],
                'password' => $password,
                'pass_salt' => $salt,
                'data_id' => $userDataId,
                'reg_code' => $userModel->getInvitationCode(),
                'tjr_path' => $tjrInfo['tjr_path'] . ',' . $tjrInfo['user_id'],
                'reg_time' => time()
            ];

            $userInfo = $userModel->addUserDataInfo($user);

            // 如果没有推荐人 就将tjr_path改为用户自己的id
            if (intval($tjrInfo['user_id']) <= 0) {
                Users::where('user_id', $userInfo['user_id'])->update(['tjr_path' => $userInfo['user_id']]);
            }
            $this->afterReg($userInfo);

            return ['code' => ReturnCode::SUCCESS_CODE];
        } catch (\Exception $e) {
            Log::write('会员注册失败: ' . $e->getMessage(), 'error');
            return ['code' => ReturnCode::ERROR_CODE];
        }
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

        return true;
    }
}