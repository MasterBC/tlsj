<?php

namespace app\wap\controller;

use think\Request;
use app\common\logic\RegLogic;
use app\common\model\About;

class Reg extends Base
{
    public function test()
    {
        create_data_city();
    }

    /**
     * 注册页面
     */
    public function index(Request $request)
    {
        $code = $request->param('code');
        $this->assign('code',$code);
        $app_address = trim(zf_cache("web_info.app_address"));
        if($app_address){
            $this->assign('app_address',$app_address);
        }else{
            $this->assign('app_address',"/");
        }
        $this->assign('phone_num',intval(zf_cache('reg_info.same_phone_register_num')));
        return view('reg/index',['registrationAgreementInfo' => About::getRegistrationAgreement(1)]);
    }

    /**
     * 处理注册操作
     * @param Request $request
     * @return \think\response\Json
     */
    public function doReg(Request $request)
    {
        $regLogic = new RegLogic();
        $data = $request->post();
        if ($data['checkbox'] != 'yes') {
            return json(['code' => -1, 'msg' => '请阅读并同意注册协议']);
        }

        $result = $this->validate($data, 'app\wap\validate\Reg');
        if ($result !== true) {
            return json(['code' => -1, 'msg' => $result]);
        }

        try {
            $regLogic->doReg();

            return json(['code' => 1, 'msg' => '注册成功']);
        } catch (\Exception $e) {
            return json(['code' => -1, 'msg' => $e->getMessage()]);
        }

    }
}
