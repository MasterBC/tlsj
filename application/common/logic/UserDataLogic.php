<?php

namespace app\common\logic;

use think\facade\Log;
use think\facade\Request;
use app\common\model\UsersData;
use think\facade\Validate;

class UserDataLogic
{
    /**
     * 修改个人信息
     * @param $userInfo
     * @param $userDataInfo
     * @return bool
     * @throws \Exception
     */
    public function updateUserData($userInfo, $userDataInfo)
    {
        //获取参数
        $username = Request::param('username');
        $email = Request::param('email');
        $qq_name = Request::param('qq_name');
        //
        try {
            $userDataInfo->username = $username;
            $userDataInfo->email = $email;
            $userDataInfo->qq_name = $qq_name;

            $userDataInfo->save();
        } catch (\Exception $e) {
            Log::write('会员修改资料失败: ' . $e->getMessage(), 'error');
            exception('修改失败');
        }
        return true;
    }

    
    /**
     * 修改呢称
     * @param $userInfo
     * @param $userDataInfo
     * @return bool
     * @throws \Exception
     */
    public function updateUserNickname($userInfo)
    {
        //获取参数
        $nickname = Request::param('nickname');
        //
        try {
            $userInfo->nickname = $nickname;
            $userInfo->save();
        } catch (\Exception $e) {
            Log::write('会员修改资料失败: ' . $e->getMessage(), 'error');
            exception('修改失败');
        }
        return true;
    }
    
    
    /**
     * 修改手机号码
     */
    public function saveMobile()
    {
        //获取参数
        $Id = Request::param('id');
        $mobile = Request::param('mobile');
        $mobileCode = Request::param('mobile_code');
        //实例化model类
        $userDataModel = new UsersData();

        //建议正式测试启用
        $mobileInfo = $userDataModel->getUserCountByMobile($mobile);
        $mobile_reg_num = intval(zf_cache('reg_info.same_phone_register_num'));
        if ($mobileInfo > $mobile_reg_num) {
            exception('该号码已存在,请重新输入!');
        }
        $userMobileInfo = $userDataModel->getUserDataField($Id, 'mobile');

        if ($userMobileInfo['mobile'] == $mobile) {
            exception('请不要输入当前号码');
        }

        // 验证短信验证码
        $validateSmsCode = validate_sms_code_verify($userMobileInfo['mobile'], $mobileCode, sid());

        if ($validateSmsCode['code'] != 1) {
            exception($validateSmsCode['msg']);
        }
        //修改数据
        $data = [
            'mobile' => $mobile
        ];
        $res = $userDataModel->updateUserData($Id, $data);
        if (!$res) {
            exception('修改失败');
        }
        return true;
    }

    /**
     * 绑定微信
     * @param $userDataInfo
     * @throws \Exception
     */
    public function bindWechat($userDataInfo)
    {
        $validate = Validate::make([
            'wx_name' => 'require',
            'wx_code' => 'require'
        ], [
            'wx_name.require' => '请输入微信账号',
            'wx_code.require' => '请上传微信收款码'
        ]);
        if (!$validate->check(Request::param())) {
            exception($validate->getError());
        }
        $userDataInfo->wx_name = Request::param('wx_name', '', 'strip_tags');
        $userDataInfo->wx_code = Request::param('wx_code', '', 'strip_tags');
        $userDataInfo->save();
    }

    /**
     * 绑定支付宝
     * @param $userDataInfo
     * @throws \Exception
     */
    public function bindAlipay($userDataInfo)
    {
        $validate = Validate::make([
            'zfb_name' => 'require',
            'zfb_code' => 'require'
        ], [
            'zfb_name.require' => '请输入支付宝账号',
            'zfb_code.require' => '请上传支付宝收款码'
        ]);
        if (!$validate->check(Request::param())) {
            exception($validate->getError());
        }
        $userDataInfo->zfb_name = Request::param('zfb_name', '', 'strip_tags');
        $userDataInfo->zfb_code = Request::param('zfb_code', '', 'strip_tags');
        $userDataInfo->save();
    }
}