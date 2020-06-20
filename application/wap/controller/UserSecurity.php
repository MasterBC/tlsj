<?php

namespace app\wap\controller;
use app\common\model\UsersSecurity;
use think\Db;
use think\Request;

class UserSecurity extends Base
{
    /**
     * 添加密保问题
     * @return \think\response\View
     */
    public function index(Request $request)
    {
        if ($request->isAjax()) {
            $data = $request->post();
            $result = $this->validate($data, 'app\wap\validate\Security.php');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            $UsersSecurityModel = new UsersSecurity();

            try {
                $UsersSecurityModel->doAddUserSecurityData($data, $this->user_id);

                return json(['code' => 0, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $this->assign('UsersSecurityInfo', DB::name('users_security')->where('uid', $this->user_id)->find());
            return view('user/set_security');
        }
    }
}
