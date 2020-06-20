<?php

namespace app\api\response;

use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;

class ReturnCode
{
    const SUCCESS_CODE = 200; // 成功状态码
    const ERROR_CODE = 400; // 操作失败
    static public $returnCode = [
        // 成功/错误返回
        '200' => 'Successful operation',
        '400' => 'Error operation',

        // 请求信息错误
        '1002' => 'Illegal request',
        '1003' => 'Token cannot be empty',
        '1004' => 'Token verification failed',
        '1005' => 'Request parameter error',

        // 账号信息错误
        '1101' => 'Account does not exist',
        '1102' => 'Wrong password',
        '1103' => 'Phone number does not exist',
        '1104' => 'Account locked',

        // 新闻错误
        '1201' => 'Article does not exist or has been deleted',

        // 注册错误
        '1301' => 'Invalid invitation code',
        '1302' => 'Account already exists',

        // 发送短信
        '1401' => 'Do not allow repeated transmissions within %s minutes',
    ];

    /**
     * 获取状态码内容
     * @param int $code 状态码
     * @param array $languageVars 动态变量值
     * @return mixed
     */
    public static function getReturnCodeMsg($code, $languageVars = [])
    {
        // 加载语言包
        Lang::load(Env::get('app_path') . '/api/lang/' . Request::param('version') . '/' . Request::header('language', 'zh-cn') . '.php');

        return Lang::get(self::$returnCode[$code], $languageVars);
    }

    /**
     * 状态信息返回
     * @access protected
     * @param  mixed $code 状态码code
     * @param  mixed $msg 提示信息
     * @param  mixed $data 要返回的数据
     * @param  array $header 发送的Header信息
     * @return \think\Response|\think\response\Json
     */
    public static function showReturnCode($code = '', $msg = '', $data = '', array $header = [])
    {
        $result = [
            'code' => self::ERROR_CODE,
            'msg' => '',
            'time' => time(),
            'data' => $code == self::SUCCESS_CODE ? $data : [],
        ];
        if (!empty($code)) {
            $result['code'] = $code;
        }
        if (!empty($msg)) {
            $result['msg'] = $msg;
        } else if (isset(self::$returnCode[$code])) {
            $result['msg'] = self::getReturnCodeMsg($code, $code != self::SUCCESS_CODE ? $data : []);
        }

        return json()->data($result)->header($header);
    }
}