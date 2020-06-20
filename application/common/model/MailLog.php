<?php

namespace app\common\model;

use think\Model;

class MailLog extends Model
{
    protected $name = 'mail_log';

    public static $sendType = [
        '1' => '注册',
        '2' => '找回密码'
    ];

    /**
     * 修改邮箱验证码日志
     * @param $where
     * @param $data
     * @return SmsLog
     */
    public function updateEmailLogData($where, $data)
    {
        return $this->where($where)->update($data);
    }

    /**
     * 查出邮箱验证码日志
     * @param $data
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEmailLogData($where, $type, $orderWhere = '')
    {
        switch ($type) {
            case 1:
                return $this->where($where)->order($orderWhere)->find();
                break;
            case 2:
                return $this->where($where)->order($orderWhere)->select();
                break;
        }
    }
}
