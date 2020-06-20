<?php

namespace app\wap\controller;

use app\common\controller\Common;
use think\facade\Session;
use app\common\model\Users;

class Base extends Common
{

    /**
     * 中间件
     * @var array
     */
    protected $middleware = [
        'CheckWapLogin',
    ];
    public $user_id; // 会员id
    public $user; // 会员信息
    public $userData; // 会员详细信息

    /**
     * 公共加载
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    protected function initialize()
    {
        parent::initialize();
        // 获取当前访问的控制器和方法名
        $controller = request()->controller();
        $action = request()->action();
        $userModel = new Users();
        $userInfo = $userModel->getSessionUserInfo();
        if ($userInfo) {
            $this->user_id = $userInfo['user_id'];
            // 重新查询用户的信息并且赋值到页面
            $this->user = $userModel->getUserByUserId($userInfo['user_id']);
            if ($this->user['session_id'] != sid() && strtolower($action) != 'outlogin') {
                $this->error('账号已在其它地方登陆', U('User/outLogin'));
            }
            if ($this->user['frozen'] != 1) {
                Users::clearSession();
                $this->error('账号已被冻结');
            }
            $this->assign('user', $this->user);
        }
        $this->assign([
            'controller' => $controller,
            'action' => $action,
        ]);
    }

    /**
     * 检测是否设置了二级密码
     */
    protected function checkPayPasswordIsSet()
    {
        if ($this->user['secpwd'] == '') {
            $this->error('未设置二级密码');
        }
    }

}
