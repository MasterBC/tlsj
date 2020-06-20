<?php

namespace app\common\model\grade;

use app\common\model\Common;
use app\common\model\Users;
use app\common\model\grade\Level;

class LevelLog extends Common
{

    protected $name = 'level_log';

    /**
     * 会员等级升级日志
     * @param $userId 用户id
     * @param $frontId 升级前id
     * @param $newId 升级后id
     * @param int $status 待审核 1
     * @param string $note 升级日志
     * @return int|string
     */
    public function addLevelLog($userId, $frontId, $newId, $status = 1, $note = '')
    {
        $data = [
            'uid' => $userId,
            'front_id' => $frontId,
            'new_id' => $newId,
            'add_time' => time(),
            'status' => $status,
            'note' => $note
        ];
        return $this->insertGetId($data);
    }

    /**
     * 自动升级会员级别
     */
    public function autoUserLevel()
    {
        $userOwnedMoneyArr = (new \app\common\model\money\UsersMoney())->where('mid', 1)->column('uid, money');
        Users::where('level', '<', '26')->field('level,user_id')
                ->chunk(5000, function($userList) use($userOwnedMoneyArr) {
                    foreach ($userList as $k => $v) {
                        $userLevelInfo = $this->autoUserMoneyGetLevel($userOwnedMoneyArr[$v['user_id']]);
                        if (empty($userLevelInfo)) {
                            continue;
                        }
                        if ($userLevelInfo['level_id'] >= $v['level']) {
                            // 添加升级日志
                            $this->addLevelLog($v['user_id'], $v['level'], $userLevelInfo['level_id'], 9, '算力达到' . $userOwnedMoneyArr[$v['user_id']] . '，自动升级');
                            $v->level = $userLevelInfo['level_id'];
                            $v->save();
                        }
                    }
                });
    }

    /**
     * 根据传的金额 查区间值 会员等级数据
     * @param $userId 用户id
     * @param $frontId 升级前id
     * @param $newId 升级后id
     * @param int $status 待审核 1
     * @param string $note 升级日志
     * @return int|string
     */
    public function autoUserMoneyGetLevel($moneyMum = 0)
    {
        $money = floatval($moneyMum);
        if ($money > 0) {
            $info = Level::where('add_mid_num', '<', $money)->where('out_mid_num', '>=', $money)->find();
            if ($info) {
                return $info;
            } else {
                return Level::where('out_mid_num', '<=', $money)->find();
            }
        }
    }

}
