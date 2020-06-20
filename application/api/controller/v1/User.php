<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\response\ReturnCode;
use app\api\service\Token;
use app\common\model\Users;
use app\common\model\UsersData;
use think\facade\Cache;
use think\facade\Request;

class User extends Base
{

    /**
     * 获取会员信息
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo()
    {
        $userInfo = Users::where('user_id', $this->user['user_id'])->find();
        $this->filterUserInfo($userInfo);
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $userInfo);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $tokenServer = new Token();
        $tokenServer->clearToken();

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
    }

    /**
     * 头像修改
     */
    public function modifyAvatar()
    {
        try {
            $upload = new \app\common\server\Upload();
            $res = $upload->uploadImageFile('head_img', 'user');
            if ($res['code'] == 1) {
                $userDataInfo = UsersData::where('id', $this->user['data_id'])->field('head,id')->find();
                $userDataInfo->head = $res['data']['src'];
                $userDataInfo->save();
                $res['data']['src'] = get_img_domain() . $res['data']['src'];
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $res['data']);
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        } catch (\Exception $e) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
        }
    }


    public function test()
    {
        echo 'V1 test action';
    }

}
