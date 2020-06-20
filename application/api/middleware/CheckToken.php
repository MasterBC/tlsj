<?php

namespace app\api\middleware;

use app\api\response\ReturnCode;
use app\api\service\Token;
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
            'login', 'reg', 'code', 'config'
        ];
        $arr = explode('.', $request->controller());
        if (count($arr) > 1) {
            $controller = $arr[1] ?? '';
        } else {
            $controller = $arr[0];
        }
//        list($version, $controller) = $controller;
        if (!in_array(strtolower($controller), $noLoginController)) {
            $tokenServer = new Token();
            $checkRes = $tokenServer->check();
            if ($checkRes !== true) {
                return $checkRes;
            }
            $userInfo = $tokenServer->getUserInfo(true);
            if (empty($userInfo['user_id'])) {
                return ReturnCode::showReturnCode(($userInfo === false) ? 1002 : $userInfo);
            }
        }

        return $next($request);
    }
}
