<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\response\ReturnCode;
use app\common\model\SmsLog;
use app\common\model\UsersData;
use think\facade\Config;
use think\facade\Request;

class Code extends Base
{

    /**
     * 根据手机号发送短信
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendVerifyCodeByPhone()
    {
        if (Config::get('sms_info.user_send_sms_switch') === false) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
        }
        $mobile = Request::param('mobile', '', 'trim');
        $type = Request::param('type', 0, 'intval');
        if ($type <= 0 || check_mobile($mobile) === false) {
            return ReturnCode::showReturnCode(1005);
        }

        switch ($type) {
            case 2:
                $info = UsersData::where('mobile', $mobile)->find();
                if (empty($info)) {
                    return ReturnCode::showReturnCode(1103);
                }
                break;
        }

        // 短信发送日志
        $smsLogData = SmsLog::where('name', $mobile)->where('status', 1)->where('is_verify', 2)->order('id', 'desc')->find();

        // 获取时间配置
        $smsTimeOut = Config::get('sms_info.send_sms_time_out', 120);

        if ($smsLogData && (time() - $smsLogData['add_time']) < $smsTimeOut) {
            return ReturnCode::showReturnCode(1401, '', [$smsTimeOut / 60]);
        }

        // 生成验证码
        $code = rand(1000, 9999);
        $time = $smsTimeOut . '秒内有效';

        // 发送短信
        $sendSms = send_sms($mobile, '验证码:' . $code . '，' . $time, $code, $type);

        if ($sendSms['status'] != 1) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
        }

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
    }

}