<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\logic\v1\UserLogic;
use app\api\response\ReturnCode;
use app\api\service\Token;

class Login extends Base
{
    /**
     * 登陆操作
     * @param UserLogic $userLogic
     * @return \think\Response|\think\response\Json
     */
    public function doLogin(UserLogic $userLogic)
    {
        $result = $userLogic->doLogin();
        if ($result['code'] != ReturnCode::SUCCESS_CODE) {
            return ReturnCode::showReturnCode($result['code']);
        } else {
            $userInfo = $result['user'];
            $this->filterUserInfo($userInfo);

            $tokenServer = new Token();
            $data = [
                'token' => $tokenServer->create($userInfo),
                'userInfo' => $userInfo
            ];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
        }
    }
}