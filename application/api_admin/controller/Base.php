<?php

namespace app\api_admin\controller;

use app\api_admin\service\Token;
use think\Controller;
use think\facade\Request;

class Base extends Controller
{
    protected $middleware = [
        'app\\api_admin\\middleware\\CheckToken',
        'app\\api_admin\\middleware\\checkAuth'
    ];

    public $adminUser; // 管理员信息

    public function initialize()
    {

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST');
        header('Access-Control-Allow-Credentials:false');
        header('Access-Control-Allow-Headers:Content-Type, X-Requested-With, Cache-Control,token');

        $tokenServer = new Token();
        $this->adminUser = $tokenServer->getUserInfo();

        Request::setModule('admin');
    }
}
