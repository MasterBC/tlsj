<?php

namespace app\wap\controller;

use app\common\model\money\MoneyLog;
use app\common\model\UsersData;
use think\Db;
use think\Request;
use app\common\model\BonusLog;
use app\common\model\Bonus as BonusModel;
use app\common\model\Users;

/**
 * Class Bonus
 */
class Bonus extends Base
{
    /**
     * 奖金日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logBonus(Request $request)
    {
        $BonusModel = new BonusModel();
        $bonusInfoType = $BonusModel->getBonusInfoField('id, name_cn');
        if ($request->isAjax()) {
            $BonusLogModel = new BonusLog();
            $BonusModel = new BonusModel();

            $bonusInfo = $BonusLogModel->getLogBonus($this->user_id);
            $bonusNameInfo = $BonusModel->getBonusInfoField('id,name_cn');

            return view('bonus/log_bonus_ajax', ['bonusInfo' => $bonusInfo, 'bonusNameInfo' => $bonusNameInfo]);
        } else {
            return view('bonus/log_bonus', ['bonusInfoType' => $bonusInfoType]);
        }
    }

    /**
     * 奖金详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logDetail(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $UsersModel = new Users();

        $bonusInfo = BonusLog::getBonusById($id);//奖金详情数据
        if (empty($bonusInfo)) {
            $this->error('非法操作');
        }
        $bonusName = BonusModel::getBonusNameById($bonusInfo['bonus_id']);
        $comeUserName = $UsersModel->getUserByUserId($bonusInfo['come_uid'], 'account');//来源会员账号
        $bonusMoneyLog = MoneyLog::where('bonus_log_id', $bonusInfo['id'])->field(Db::raw('sum(money) money').',mid')->select();
        return view('bonus/log_detail', [
            'bonusName' => $bonusName,
            'bonusInfo' => $bonusInfo,
            'userAccount' => $this->user,
            'comeUserName' => $comeUserName,
            'moneyNames' => \app\common\model\money\Money::getMoneyNames(),
            'bonusMoneyLog' => $bonusMoneyLog,
        ]);
    }
}