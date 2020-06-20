<?php

namespace app\api_admin\middleware;

use app\api_admin\response\ReturnCode;
use app\api_admin\service\Token;
use think\facade\Response;
use think\Request;

class CheckToken
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->isOptions()) {
            return Response::code(200);
        }
        $noLoginController = [
            'login', 'reg'
        ];
        $controller = $request->controller();
//        list($version, $controller) = $controller;
        if (!in_array(strtolower($controller), $noLoginController)) {
            $tokenServer = new Token();
            $checkRes = $tokenServer->check();
            if ($checkRes !== true) {
                return $checkRes;
            }
            $userInfo = $tokenServer->getUserInfo();
            if (empty($userInfo['admin_id'])) {
                return ReturnCode::showReturnCode(ReturnCode::LOGIN_CODE, '非法操作');
            }
        }

        return $next($request);
    }
}
