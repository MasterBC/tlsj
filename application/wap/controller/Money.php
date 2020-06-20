<?php

namespace app\wap\controller;

use app\common\model\Users;
use app\common\model\UserAuthName;
use think\Request;
use app\common\model\money\MoneyLog;
use app\common\model\money\UsersMoney;
use app\common\model\money\Money as MoneyModel;
use app\common\model\work\MoneyWebDay;
use app\common\model\product\UsersProduct;
use app\common\model\product\Product as ProductModel;
use think\helper\Time;

/**
 * Class Money
 * @package app\wap\controller
 */
class Money extends Base
{

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->assign('productNameArr', ProductModel::getProductNames());
    }

    /**
     * 钱包日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logMoney(Request $request)
    {
        if ($request->isAjax()) {
            $MoneyLogModel = new MoneyLog();
            $MoneyModel = new MoneyModel();

            $moneyInfo = $MoneyModel->getMoneyInfoField('money_id,logo'); //货币数据
            $moneyData = $MoneyLogModel->getLogMoney($this->user_id);
            return view('money/log_money_ajax', ['moneyData' => $moneyData, 'moneyInfo' => $moneyInfo]);
        } else {
            return view('money/log_money', ['moneyLogTypes' => money_log_type()]);
        }
    }

    /**
     * 钱包日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function threeLogMoney(Request $request)
    {
        if ($request->isAjax()) {
            $MoneyLogModel = new MoneyLog();
            $MoneyModel = new MoneyModel();
            $moneyInfo = $MoneyModel->getMoneyInfoField('money_id,logo'); //货币数据
            $moneyData = $MoneyLogModel->getMidLogMoney($this->user_id, 3);
            return view('money/three_money/log_money_ajax', ['moneyData' => $moneyData, 'moneyInfo' => $moneyInfo]);
        } else {
            return view('money/three_money/log_money', ['moneyLogTypes' => money_log_type()]);
        }
    }

    /**
     * 获取算力
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAjaxUserOwnedMoneyArr(Request $request, UsersMoney $usersMoneyModel, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isAjax()) {
            $userOwnedMoney = $usersMoneyModel->getUserOwnedMoney($this->user_id);
            $webDayMoney = $moneyWebDayModel->getWebdayMoney();
            foreach ($userOwnedMoney as $k => $v) {
                if ($k == 2) {
                    $userOwnedMoney[$k] = (int) $v;
                } else {
                    $userOwnedMoney[$k] = (float) $v;
                }
            }
            return json(['code' => 1, 'userOwnedMoneyAll' => $userOwnedMoney, 'webDayMoney' => $webDayMoney]);
        }
    }

    /**
     * 获取平台总收入
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAjaxWebMoneyDay(Request $request, UsersMoney $usersMoneyModel, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isAjax()) {
            $userOwnedMoney = $usersMoneyModel->getUserOwnedMoney($this->user_id);
            foreach ($userOwnedMoney as $k => $v) {
                if ($k == 2) {
                    $userOwnedMoney[$k] = (int) $v;
                } else {
                    $userOwnedMoney[$k] = (float) $v;
                }
            }
            $webDayMoney = $moneyWebDayModel->getWebdayMoney();
            $webDayMoney['totao_level'] = UsersProduct::where('product_id', 45)->count();
            $webDayMoney['stay_level'] = intval(zf_cache('security_info.web_totao_level')) - $webDayMoney['totao_level'];
            $fhsy['zrsy'] = MoneyLog::where(['is_type'=>178,'uid'=>$this->user_id])->whereBetween('edit_time', Time::yesterday())->sum('money');
            $fhsy['jrsy'] = MoneyLog::where(['is_type'=>178,'uid'=>$this->user_id])->whereBetween('edit_time', Time::today())->sum('money');
            $fhsy['lszsy'] = MoneyLog::where(['is_type'=>178,'uid'=>$this->user_id])->sum('money');
            return json(['code' => 1, 'userOwnedMoneyAll' => $userOwnedMoney, 'webDayMoney' => $webDayMoney, 'fhsy' => $fhsy]);
        }
    }

    /**
     * 获取平台总收入
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function webMoneyDayDetails(Request $request, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isAjax()) {
            $webDayMoney = $moneyWebDayModel->getWebdayMoney();
            return json(['code' => 1, 'webDayMoney' => $webDayMoney]);
        } else {
            $usersProductModel = new UsersProduct();
            $dayProduct = $usersProductModel->whereBetween('add_time', Time::today())->where('product_id', 45)->select();
            $userIds = array_unique(get_arr_column($dayProduct, 'user_id'));
            $usersIdArr = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $userNameIdArr = UserAuthName::whereIn('uid', $userIds)->column('username', 'uid');
            return view('money/day_money_detail', [
                'userIdArr' => $usersIdArr,
                'dayProduct' => $dayProduct,
                'userNameIdArr' => $userNameIdArr,
            ]);
        }
    }

}
