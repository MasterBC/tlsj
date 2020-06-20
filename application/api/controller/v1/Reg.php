<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\logic\v1\UserLogic;
use app\api\response\ReturnCode;

class Reg extends Base
{

    /**
     * 会员注册
     * @param UserLogic $userLogic
     * @return \think\Response|\think\response\Json
     */
    public function doReg(UserLogic $userLogic)
    {
        $result = $userLogic->doReg();
        if ($result['code'] != ReturnCode::SUCCESS_CODE) {
            return ReturnCode::showReturnCode($result['code']);
        } else {
            $data = [];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
        }
    }

}