<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\api_admin\service\Token;
use app\api_admin\logic\AdminUserLogic;
use think\facade\Request;

class Login extends Base
{
    /**
     * 登陆操作
     * @param AdminUserLogic $adminUserLogic
     * @return \think\Response|\think\response\Json
     */
    public function doLogin(AdminUserLogic $adminUserLogic)
    {
        if (Request::isPost()) {
            try {
                $userInfo = $adminUserLogic->doLogin();

                $tokenServer = new Token();
                $data = [
                    'token' => $tokenServer->create($userInfo)
                ];
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
            } catch (\Exception $e) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $e->getMessage());
            }
        }
    }
}
