<?php

namespace app\common\logic;

use think\facade\Request;
use app\common\model\claw\Claw as ClawModel;
use app\common\model\claw\ClawLog;
use app\common\model\money\UsersMoney;
use think\Db;

class AppLogic
{
    /**
     * 欢乐夹娃娃
     * @param $userInfo
     * @return bool|\think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function clawIndex($userInfo)
    {
        $id = Request::param('id');
        if ($id <= 0) {
            exception('网络错误，请刷新后重试');;
        }

        $ClawModel = new ClawModel();
        $clawInfo = $ClawModel->where(['id' => $id])->find();

        if (!$clawInfo) {
            exception('网络错误，请刷新后重试');
        }

        $clawLogData = [];
        $clawLogData['add_time'] = time();
        $clawLogData['uid'] = $userInfo['user_id'];
        $clawLogData['c_id'] = $id;
        $clawLogData['mid'] = $clawInfo['mid'];
        $clawLogData['money'] = $clawInfo['num'];

        $clawLogModel = new ClawLog();
        $usersMoneyModel = new UsersMoney();

        Db::startTrans();
        $res = $clawLogModel->insertGetId($clawLogData);

        $info = $usersMoneyModel->amountChange($userInfo['user_id'], $clawInfo['mid'], '-'.zf_cache('security_info.claw_num'), 109, '欢乐夹娃娃');
        if (!$res || !$info) {
            Db::rollback();
            exception('网络错误，请刷新后重试');
        }
        Db::commit();
        return true;
    }
}