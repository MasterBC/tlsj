<?php

namespace app\common\model;

use think\Model;

class SmsLog extends Model
{
    protected $name = 'sms_log';

    public static $sendType = [
        '1' => '注册',
        '2' => '找回密码'
    ];

    /**
     * 添加手机验证码日志
     * @param $data
     * @return int|string
     */
    public function smsAddData($data)
    {
        $data['name'] = $data['mobile'];
        $data['add_time'] = time();
        return $this->insert($data);
    }

    /**
     * 修改手机验证码日志
     * @param $where
     * @param $data
     * @return SmsLog
     */
    public function updateSmsLogData($where, $data)
    {
        return $this->where($where)->update($data);
    }

    /**
     * 查出手机验证码日志
     * @param $data
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSmsLogData($where, $type, $orderWhere = '')
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
