<?php

namespace app\common\server;

use app\common\model\Users;
use app\common\model\UsersLog;
use app\common\model\money\UsersMoney;

class AutomaticMining
{

    /**
     * 挖矿收益
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function automaticMining()
    {
        $paramData = explode('-', zf_cache('security_info.pd_money_base_time'));
        $webPastDueTime = zf_cache('security_info.experience');
        $userList = Users::where(['activate' => 1])->field('user_id')->select();

        if ($userList) {
            $addTime = 0;
            foreach ($userList as $v) {
                $userMoneyIdArr = (new UsersMoney())->getUserOwnedMoney($v['user_id']);
                foreach ($paramData as $key => $val) {
                    // 计算经验值
                    $addNum = number_format(((((1 / 86400) / $webPastDueTime) * ($val * 3600)) * $userMoneyIdArr[2]), 4);
                    if ($addNum < 0.0001) {
                        continue;
                    }
                    // 组合数据S
                    $data = [];
                    $data['uid'] = $v['user_id'];
                    $data['add_time'] = $key == 0 ? time() : $addTime;
                    $data['add_num'] = $addNum;
                    $data['experience'] = $webPastDueTime;
                    $data['out_time'] = time() + $val * 3600;
                    $data['base_num'] = $userMoneyIdArr[2];
                    $data['base_time'] = $val * 3600;
                    $data['status'] = 1;
                    $addTime = $data['out_time'] + $val * 3600;
                    // 组合数据E
                    // 添加数据
                    PdMoneyProduct::insert($data);
                }
            }
        }
    }

    /**
     * 检测已过期
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function outOfOffice()
    {
        $time = time() - 86400;
        $list = PdMoneyProduct::where(['status' => 1])->where('add_time', '<=', $time)->select();

        if ($list) {
            $userLog = new UsersLog();
            foreach ($list as $v) {
                $v->status = 7;
                $v->cj_time = time();
                $v->save();
                $userLog->addLog($v['uid'], 4, '鱼币未领取过期，订单id:' . $v['id'] . ',金额:' . $v['add_num']);
            }
        }
    }

}
