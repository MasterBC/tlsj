<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\common\logic\AdminUserLogic;
use app\facade\Password;

class Login extends Controller
{
    protected $middleware = ['checkAdminLogin'];

    public function index()
    {

        /*$salt = Password::getPasswordSalt();
        $password = Password::getPassword('admin999', 'p4j8i9kr7gxptotf939t');
        echo $salt;
        echo "<br />";
        echo $password;
        die;*/

        return view('login/index');
    }

    /**
     * 登录
     */
    public function doLogin(Request $request)
    {
        $adminUserLogic = new AdminUserLogic();

        $result = $this->validate($request->post(), 'app\admin\validate\Login');
        if ($result !== true) {
            return json(['code' => -1, 'msg' => $result]);
        }

        try {
            $adminUserLogic->doLogin();

            return json(['code' => 1, 'msg' => '登陆成功']);
        } catch (\Exception $e) {
            return json(['code' => -1, 'msg' => $e->getMessage()]);
        }
    }

    public function test2()
    {
        return view('Login/test2');
    }

}
