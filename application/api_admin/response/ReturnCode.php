<?php

namespace app\api_admin\response;

use think\facade\Env;
use think\facade\Lang;
use think\facade\Request;

class ReturnCode
{
    const SUCCESS_CODE = 200; // 成功状态码
    const ERROR_CODE = 400; // 操作失败
    const LOGIN_CODE = 1001; // 登录状态失效的状态码


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
            'msg' => $msg,
            'time' => time(),
            'data' => $code == self::SUCCESS_CODE ? $data : [],
        ];
        if (!empty($code)) {
            $result['code'] = $code;
        }
        if ($msg == '' && $code == self::SUCCESS_CODE) {
            $result['msg'] = '操作成功';
        }
        if ($msg == '' && $code == self::ERROR_CODE) {
            $result['msg'] = '操作失败';
        }

        return json()->data($result)->header($header);
    }
}