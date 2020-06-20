<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
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
        $info = $adminUserModel->getUserById($this->adminUser['admin_id']);
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\AdminUserForm.edit');
            if ($result !== true) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $result);
            }
            try {
                $adminLogic->editAdminUser($info);

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Log::write('管理员修改自己的资料: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $e->getMessage());
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $info->toArray());
        }
    }
}