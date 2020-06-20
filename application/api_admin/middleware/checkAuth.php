<?php

namespace app\api_admin\middleware;

use app\api_admin\response\ReturnCode;
use app\api_admin\service\Token;
use think\auth\Auth;

class checkAuth
{
    public function handle($request, \Closure $next)
    {
        $noCheckAuthController = [
            'login', 'reg', 'index'
        ];
        $controller = $request->controller();
        if (!in_array(strtolower($controller), $noCheckAuthController)) {
            $controller = $request->controller();
            $action = $request->action();

            $tokenServer = new Token();

            $adminUserInfo = $tokenServer->getUserInfo();
            $adminId = $adminUserInfo['admin_id'];

            // 获取auth实例
            $auth = Auth::instance();

            if (!$auth->check($controller . '/' . $action, $adminId)) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '没有权限');
            }
        }

        return $next($request);
    }
}
