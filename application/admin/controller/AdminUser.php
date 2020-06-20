<?php

namespace app\admin\controller;

use think\facade\Log;
use think\Request;
use app\common\model\AdminUser as AdminUserModel;
use app\common\logic\AdminUserLogic;

class AdminUser extends Base
{

    /**
     * 修改信息
     */
    public function editInfo(Request $request, AdminUserModel $adminUserModel, AdminUserLogic $adminLogic)
    {
        $info = $adminUserModel->getAdminUserInfo();
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\AdminUserForm.edit');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $adminLogic->editAdminUser($info);

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('管理员修改自己的资料: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $this->assign('info', $info);
            return view('admin_user/edit_info');
        }
    }
}