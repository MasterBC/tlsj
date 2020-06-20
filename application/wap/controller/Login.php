<?php

namespace app\wap\controller;

use think\Request;
use app\common\logic\LoginLogic;
use Session;

class Login extends Base
{
    /**
     * 账号密码登录操作
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function accountIndex(Request $request)
    {
        if ($request->isPost()) {
            $WapUserLogic = new LoginLogic();
            $data = $request->post();
            $result = $this->validate($data, 'app\wap\validate\Login.Account');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $WapUserLogic->doLoginAccount();
                return json(['code' => 1, 'msg' => '登陆成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
        return view('login/account_index');
    }

    /**
     * 邮箱验证码登录
     */
    public function mailboxIndex()
    {
        return view('login/mailbox_index');
    }

    /**
     * 手机验证码登录操作
     * @param Request $request
     * @return \think\response\View
     */
    public function mobileIndex(Request $request)
    {
        if ($request->isPost()) {
            $WapUserLogic = new LoginLogic();
            $data = $request->post();
            $result = $this->validate($data, 'app\wap\validate\Login.Mobile');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $WapUserLogic->doLoginMobile();

                return json(['code' => 1, 'msg' => '登陆成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }

        return view('login/mobile_index');
    }
}
