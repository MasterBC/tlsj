<?php

namespace app\wap\controller;

use app\common\model\BonusLog;
use app\common\model\money\UsersMoney;
use think\Request;
use think\Db;
use app\common\model\Users;
use app\common\model\UsersRankMoney;
use app\common\server\Log;
use app\common\model\UsersRedEnvelopeLog;
use app\common\model\product\UsersProduct;
use app\common\server\bonus\Server;
use think\helper\Time;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Assets extends Base
{

    /**
     * 数字资产首页
     * @param Request $request
     * @return type
     */
    public function assetsIndex(Request $request, Users $usersModel)
    {

        $rankInfo = UsersRankMoney::where('id', $this->user['rank_money_id'])->find();

        $unsettlementBonusAmount = BonusLog::where('uid', $this->user_id)
                ->whereIn('status', [1, 2])
                ->sum('money');
//        $settledBonusAmount = BonusLog::where('uid', $this->user_id)
//                ->where('status', 9)
//                ->sum('money');
//        $rankInfo = UsersRankMoney::where('target_money', '>=', $settledBonusAmount)
//                ->order('sort', 'asc')
//                ->find();
        // 活跃收益
        $dynamicBonusAmount = BonusLog::where('uid', $this->user_id)
                ->whereIn('bonus_id', [2])
                ->whereIn('status', [1, 2])
                ->sum('money');
        //  有牛共享收益
        $cattleBonusAmount = BonusLog::where('uid', $this->user_id)
                ->whereIn('bonus_id', [1])
                ->whereIn('status', [1, 2])
                ->sum('money');

        if (intval($dynamicBonusAmount + $cattleBonusAmount) <= 0) {
            $lockPer = 0;
        } else {
            $lockPer = (($dynamicBonusAmount + $cattleBonusAmount) / $rankInfo['target_money']) * 100;
        }


        //  推荐人数
        $tjrNum = Users::where('tjr_id', $this->user_id)->count();
        //  签到次数
        $signNum = UsersRedEnvelopeLog::where('uid', $this->user_id)->count();
        //  38 级人数
        $productNum = UsersProduct::where('user_id', $this->user_id)->where('product_id', '>', 37)->count();

        $tjrPer = $tjrNum * zf_cache('security_info.tjr_num_plus');
        $signPer = $signNum * zf_cache('security_info.sign_num_plus');
        $closePer = $productNum * zf_cache('security_info.close_num_plus');
        if (($tjrPer + $signPer + $closePer) > 100) {
            $per = 100;
        } else {
            $per = $tjrPer + $signPer + $closePer;
        }

        $day_first_bonus = BonusLog::where('uid', $this->user_id)->where('layer', 1)->whereBetween('add_time', Time::today())->sum('money');
        $day_second_bonus = BonusLog::where('uid', $this->user_id)->where('layer', 2)->whereBetween('add_time', Time::today())->sum('money');

        $zuor_first_bonus = BonusLog::where('uid', $this->user_id)->where('layer', 1)->whereBetween('add_time', Time::yesterday())->sum('money');
        $zuor_second_bonus = BonusLog::where('uid', $this->user_id)->where('layer', 2)->whereBetween('add_time', Time::yesterday())->sum('money');

        if ($request->isPost()) {
            Db::startTrans();
            try {
                if ($unsettlementBonusAmount <= $rankInfo['target_money']) {
                    return json()->data([
                                'code' => -1,
                                'msg' => '未达到条件'
                    ]);
                }
                BonusLog::where('uid', $this->user_id)
                        ->whereIn('status', [1, 2])
                        ->update([
                            'status' => 9,
                            'last_time' => time()
                ]);
                $userMoneyModel = new UsersMoney();
                $userMoneyModel->amountChange($this->user_id, 3, $unsettlementBonusAmount, 1, '划转', [
                    'come_uid' => $this->user_id
                ]);
                Users::where(['user_id' => $this->user_id])->update([
                    'rank_money_id' => $this->user['rank_money_id'] + 1
                ]);
                Db::commit();
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                Db::rollback();
                Log::exceptionWrite('划转奖励失败', $e);
                return json()->data([
                            'code' => -1,
                            'msg' => '操作失败'
                ]);
            }
        } else {

            $nextRankInfo = UsersRankMoney::where('sort', '>=', $rankInfo['sort'])
                    ->where('id', '<>', $rankInfo['id'])
                    ->order('sort', 'asc')
                    ->find();

            return view('assets/assets_index', [
                'rankInfo' => $rankInfo,
                'nextRankInfo' => $nextRankInfo,
//                'settledBonusAmount' => $settledBonusAmount,
                'unsettlementBonusAmount' => $unsettlementBonusAmount,
                'cattleBonusAmount' => $cattleBonusAmount,
                'lockPer' => $lockPer,
                'per' => $per,
                'dynamicBonusAmount' => $dynamicBonusAmount,
                'day_first_bonus' => $day_first_bonus,
                'day_second_bonus' => $day_second_bonus,
                'dayBonus' => $day_first_bonus + $day_second_bonus,
                'zuor_first_bonus' => $zuor_first_bonus,
                'zuor_second_bonus' => $zuor_second_bonus,
                'zuorBonus' => $zuor_first_bonus + $zuor_second_bonus,
            ]);
        }
    }

    public function profitAndBonus()
    {
        $rankList = UsersRankMoney::where('id', '>', '0')->select();

        return view('assets/profit_index', [
            'rankList' => $rankList
        ]);
    }

}
