<?php

namespace app\wap\controller;

use think\Controller;
use think\Request;
use Session;
use app\common\logic\ForgotLogic;


class Forgot extends Controller
{
    /**
     * 找回密码页面
     */
    public function index()
    {
        return view('forgot/index');
    }

    /**
     * 处理账号密码操作
     */
    public function doForgot(Request $request)
    {
        $ForgotLogic = new ForgotLogic();
        $data = $request->post();

        $result = $this->validate($data, 'app\wap\validate\Forgot');
        if ($result !== true) {
            return json(['code' => -1, 'msg' => $result]);
        }

        // 模拟找回密码三种方式
        $type = 1;
        if ($type == 1) {
            $errorMsg = '请输入手机验证码';
        } elseif ($type == 2) {
            $errorMsg = '请输入邮箱验证码';
        } elseif ($type == 3) {
            $errorMsg = '请输入密保信息';
        }

        if ($data['mobileCode'] == '') {
            return json(['code' => -1, 'msg' => $errorMsg]);
        }

        try {
            $ForgotLogic->doForgot();

            return json(['code' => 1, 'msg' => '操作成功']);
        } catch (\Exception $e) {
            return json(['code' => -1, 'msg' => $e->getMessage()]);
        }
    }
}
