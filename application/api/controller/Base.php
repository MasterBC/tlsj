<?php

namespace app\api\controller;

use app\api\response\ReturnCode;
use app\api\service\Token;
use app\common\model\Users;
use app\common\model\UsersData;
use org\Jwt;
use think\Controller;
use think\facade\Request;
use think\facade\Cache;

class Base extends Controller
{
    protected $user; // 会员信息

    /**
     * 初始化
     * @return \think\Response|\think\response\Json|void
     */
    protected function initialize()
    {
        $noLoginController = [
            'login', 'reg', 'code', 'config'
        ];
        $arr = explode('.', Request::controller());
        if (count($arr) > 1) {
            $controller = $arr[1] ?? '';
        } else {
            $controller = $arr[0];
        }
//        list($version, $controller) = $controller;
        if (!in_array(strtolower($controller), $noLoginController)) {
            $tokenServer = new Token();
            $this->user = $tokenServer->getUserInfo();
        }
    }

    /**
     * 过滤不需要返回的信息
     * @param $userInfo
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function filterUserInfo(&$userInfo)
    {
        $userDataInfo = UsersData::where('id', $userInfo['data_id'])->field('head,mobile')->find();
        $userInfo['head'] = get_img_show_url($userDataInfo['head'] ?? '');
        $userInfo['mobile'] = $userDataInfo['mobile'] ?? '';

        if (isset($userInfo['pass_salt'])) {
            unset($userInfo['pass_salt']);
        }
        if (isset($userInfo['password'])) {
            unset($userInfo['password']);
        }
        if (isset($userInfo['secpwd'])) {
            unset($userInfo['secpwd']);
        }
    }
}