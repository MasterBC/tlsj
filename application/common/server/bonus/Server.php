<?php

namespace app\common\server\bonus;

use app\common\model\Bonus;
use app\common\model\BonusLog;
use app\common\model\Users;

class Server
{

    // 奖金设置信息
    private $bonus = [];
    // 要添加的奖金记录
    private $bonusLogs = [];
    // 奖金日志model
    private $bonusLogModel = [];

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->bonusLogModel = new BonusLog();
    }

    /**
     * 推荐奖
     *
     * @param float $yj 业绩
     * @param array $userInfo 会员信息
     * @author gkdos
     */
    public function recommendedAward($yj, $userInfo, $bonusId = 1)
    {
        if ($yj <= 0) {
            return;
        }
        $pers = explode('-', zf_cache('security_info.bonus_config', ''));

        $tjrIds = explode(',', $userInfo['tjr_path']);
        $tjrIds = array_reverse($tjrIds);
        $num = count($pers);
        foreach ($tjrIds as $k => $v) {
            if ($k > $num) {
                unset($tjrIds[$k]);
            }
        }
        $bonusInfo = $this->getBonusInfo($bonusId);
        $tjrList = Users::whereIn('user_id', $tjrIds)->column('account,level', 'user_id');
        foreach ($tjrIds as $k => $v) {
            $tjrInfo = $tjrList[$v] ?? [];
            if (empty($tjrInfo)) {
                continue;
            }
            $per = (float) ($pers[$k] ?? 0);
            if ($per <= 0) {
                continue;
            }
            $amount = $yj * $per / 100;

            $this->addBonusLog(
                    $bonusInfo['id'], $tjrInfo['user_id'], $userInfo['user_id'], $amount, $amount, $k + 1, false
            );
        }
    }

    /**
     * 奖金结算
     *
     * @throws \Exception
     */
    public function clear()
    {
        if (!empty($this->bonusLogs)) {
            foreach ($this->bonusLogs as $k => $v) {
                $bonusLogInfo = $v;

                $money = $v['money'];

                if (!$bonusLogInfo['is_settlement']) {
                    $bonusLogInfo['status'] = 1;
                    $bonusLogInfo['last_money'] = $money;
                    $bonusLogInfo['last_time'] = time();
                    $bonusLogInfo['stay_money'] -= $money;
                }
                unset($bonusLogInfo['is_settlement']);

                ksort($bonusLogInfo);
                $this->bonusLogs[$k] = $bonusLogInfo;
            }
            $this->bonusLogModel->limit(500)->insertAll($this->bonusLogs);
            $this->bonusLogs = [];
            unset($moneyLogs, $usersMoneyChange);
        }
    }

    /**
     * 添加奖金记录
     *
     * @param int $bonusId 奖金Id
     * @param int $userId 拿奖会员id
     * @param int $comeUserId 来源会员id
     * @param float|int $comeMoney 应得奖金
     * @param float|int $money 实发奖金
     * @param int $layer 层/代数
     * @param int $isSettlement 是否结算
     * @return void
     */
    private function addBonusLog($bonusId, $userId, $comeUserId, $comeMoney, $money, $layer = 1, $isSettlement = true)
    {
        if ($comeMoney > 0) {
            $data = $this->bonusLogModel->generatingData(
                    $bonusId, $userId, $comeUserId, $comeMoney, $money, $layer
            );
            $data['is_settlement'] = $isSettlement;
            $this->bonusLogs[] = $data;
        }
    }

    /**
     * 获取奖金配置
     *
     * @param int $bonusId 奖金配置id
     * @return array
     */
    private function getBonusInfo($bonusId)
    {
        if (!isset($this->bonus[$bonusId])) {
            $this->bonus[$bonusId] = Bonus::getInfoById($bonusId);
        }

        return $this->bonus[$bonusId] ?? [];
    }

}
