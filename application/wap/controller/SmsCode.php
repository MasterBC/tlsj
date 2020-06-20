<?php

namespace app\wap\controller;

use think\Request;
use app\common\logic\SendLogic;

class SmsCode extends Base
{
    /**
     * 发送手机验证码
     * @param Request $request
     * @return \think\response\Json
     */
    public function sendSmsRegCode(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $sendLogicModel = new SendLogic();
            $type = $request->param('type', '1', 'intval');
            try {
                $sendLogicModel->sendSmsRegCode($data, $type);

                return json(['code' => 1, 'msg' => '发送成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

    /**
     * 发送邮箱验证码
     * @param Request $request
     * @return \think\response\Json
     */
    public function sendEmailRegCode(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $sendLogicModel = new SendLogic();
            try {
                $sendLogicModel->sendEmailRegCode($data);

                return json(['code' => 1, 'msg' => '发送成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }
}
