<?php

namespace app\wap\controller;

use think\Request;
use app\common\model\Bank;
use app\common\model\money\Money;
use app\common\logic\RechargeLogic;
use app\common\model\money\UsersMoneyAdd;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class MoneyAdd extends Base
{
    public function __construct()
    {
        parent::__construct();
        $statusData = [1 => '待审核', 3 => '己拒绝', 9 => '己审核'];
        $this->assign('statusData', $statusData);
        $this->assign('moneyNames', get_money_name());
    }

    /**
     * 充值列表
     * @param Request $request
     * @param UsersMoneyAdd $usersMoneyAddModel
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addMoneyIndex(Request $request, UsersMoneyAdd $usersMoneyAddModel)
    {
        if ($request->isAjax()) {
            // 根据条件查出数据
            $addMoneyData = $usersMoneyAddModel->getMoneyAddLog($this->user_id);
            $this->assign('addMoneyData', $addMoneyData);
            return view('add_money/add_index_ajax');
        } else {
            return view('add_money/add_index');
        }
    }

    /**
     * 充值详情
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\response\Redirect|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addMoneyDetail(Request $request, Bank $bankModel)
    {
        $id = $request->param('id', '' , 'intval');

        if ($id <= 0) {
            return redirect(U('money_add/addmoneyindex'));
        }

        $usersMoneyAddInfo = UsersMoneyAdd::where('id', $id)->find();
        $this->assign('info', $usersMoneyAddInfo);
        $this->assign('bankNames', $bankModel->getBankNames());
        return view('add_money/add_detail');
    }

    /**
     * 我要充值
     * @param Request $request
     * @param \app\common\model\Bank $bankModel
     * @param RechargeLogic $rechargeLogic
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addMoney(Request $request, \app\common\model\Bank $bankModel, RechargeLogic $rechargeLogic)
    {
        if ($request->isPost()) {
            try {
                $rechargeLogic->addMoney($this->user);
                return json()->data(['code' => 1, 'msg' => '充值成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $bankList = $bankModel->getRechargeBank();
            $bankArr = [];
            foreach ($bankList as $v) {
                $bankArr[] = [
                    'value' => $v['id'],
                    'text' => $v['name_cn'],
                    'account' => $v['account'],
                    'address' => $v['address'],
                    'username' => $v['username'],
                    'code' => $v['code'],
                ];
            }

            if (empty($bankArr)) {
                $this->error('暂时不能充值');
            }

            $moneyArr = [];
            $moneyList = Money::where(['status' => 1])->field('money_id, name_cn')->select();
            foreach ($moneyList as $v) {
                $moneyArr[] = [
                    'value' => $v['money_id'],
                    'text' => $v['name_cn'] . '余额：'. get_money_amount($this->user_id, $v['money_id'], 1),
                ];
            }

            $assignData = [
                'bankArr' => $bankArr,
                'bankStr' => json_encode($bankArr),
                'moneyArr' => $moneyArr,
                'moneyStr' => json_encode($moneyArr)
            ];

            return view('add_money/add_money', $assignData);
        }
    }

    /**
     * 如果有多个钱包就显示
     * @param Request $request
     * @return type
     */
    public function addMoneyInfo(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('add_money/add_info');
        }
    }

}
