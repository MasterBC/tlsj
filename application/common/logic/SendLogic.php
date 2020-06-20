<?php

namespace app\common\logic;

use app\common\model\SmsLog;
use app\common\model\MailLog;
use think\facade\Session;
use app\common\model\UsersData;

class SendLogic
{

    /**
     * 处理发送手机短信验证码
     * @param array $data 发送的参数
     * @param int $type 发送类型
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendSmsRegCode($data, $type)
    {
        $data['mobile'] = trim($data['mobile']);
        if (!check_mobile($data['mobile'])) {
            exception('请输入正确的手机号');
        }

        // 判断图形验证码是否正确
        if (zf_cache('sms_info.verif_code') == 1) {
            if (!captcha_check($data['code'], $data['check_code'])) {
                exception('图形验证码不正确');
            }
        }

        switch ($type) {
            case 2:
                $info = Users::where('account', $data['mobile'])->find();
                if (empty($info)) {
                    exception('该手机号不存在');
                }
                break;
        }

        if (zf_cache('sms_info.user_send_sms_switch') === false) {
            exception('平台短信正在维护中...');
        }

        $smsLogModel = new SmsLog();
        // 判断验证码是否存在
        $where['name'] = $info['mobile'];
        $where['session_id'] = sid();
        $where['status'] = 1;
        $where['is_verify'] = 2;
        $smsLogData = $smsLogModel->getSmsLogData($where, 1, 'id desc');
        // 获取时间配置
        $smsTimeOut = zf_cache('sms_info.send_sms_time_out') ? zf_cache('sms_info.send_sms_time_out') : 120;

        // 120秒以内不可重复发送
        if ($smsLogData && (time() - $smsLogData['add_time']) < $smsTimeOut) {
            exception(zf_cache('sms_info.send_sms_time_out') . '秒内不允许重复发送');
        }

        // 生成验证码
        $code = rand(1000, 9999);
        $time = zf_cache('sms_info.send_sms_time_out') . '秒内有效';

        $sendSms = send_sms($data['mobile'], '验证码:' . $code . '，' . $time, $code);

        if ($sendSms['status'] != 1) {
            exception($sendSms['msg']);
        }

        return true;
    }

    /**
     * 发送邮箱验证码
     * @param $data
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendEmailRegCode($data)
    {
        $data['email'] = trim($data['email']);
        if (!check_mail($data['email'])) {
            exception('请输入正确的邮箱号');
        }

        $smtpEmailModel = new MailLog();
        // 判断验证码是否存在
        $where['email'] = $data['email'];
        $where['session_id'] = sid();
        $where['status'] = 1;
        $where['is_verify'] = 2;
        $smtpLogData = $smtpEmailModel->getEmailLogData($where, 1, 'id desc');
        // 获取时间配置
        $smtpTimeOut = zf_cache('smtp_info.send_email_time_out') ? zf_cache('smtp_info.send_email_time_out') : 120;

        // 120秒以内不可重复发送
        if ($smtpLogData && (time() - $smtpLogData['add_time']) < $smtpTimeOut) {
            exception(zf_cache('smtp_info.send_email_time_out') . '秒内不允许重复发送');
        }

        // 生成验证码
        $code = rand(1000, 9999);
        $time = zf_cache('smtp_info.send_email_time_out') . '秒内有效';

        $sendSmtp = send_mail($data['email'], $data['email'], '注册', '验证码:' . $code . '，' . $time, $code);
        if (!$sendSmtp) {
            exception('发送失败');
        }

        return true;
    }

}
