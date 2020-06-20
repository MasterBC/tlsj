<?php

namespace app\wap\controller;

use app\common\model\UsersData;
use think\Request;
use app\common\model\Bank;
use app\common\model\UserBank;
use app\common\logic\MoneyLogic;
use app\common\model\money\MoneyLog;
use app\common\model\money\MoneyCarryBankLog;
use app\common\model\money\MoneyCarry as MoneyCarryModel;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class MoneyCarry extends Base
{
    /**
     * 提现列表
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexCarry(Request $request)
    {
        if ($request->isAjax()) {
            $moneyCarryBankLogModel = new MoneyCarryBankLog();
            $list = $moneyCarryBankLogModel->getMoneyCarryLog($this->user_id);
            $this->assign('list', $list);
            $this->assign('moneyNames', get_money_name());
            return view('carry_money/carry_index_ajax');
        } else {
            return view('carry_money/carry_index');
        }
    }

    /**
     * 提现详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function carryDetail(Request $request)
    {
        $carrynInfoId = $request->param('id', 0, 'intval');
        $moneyCarryInfo = MoneyCarryBankLog::where('id', $carrynInfoId)->find();;
        if(empty($moneyCarryInfo)) {
            $this->error('参数错误');
        }

        $assignData = [
            'moneyNames' => get_money_name(),
            'info' => $moneyCarryInfo,
            'statusData' => MoneyCarryBankLog::$status
        ];
        return view('carry_money/carry_detail', $assignData);
    }

    /**
     * 提现申请
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function carryAdd(Request $request)
    {
        if ($request->isAjax()) {
//            if ($this->user['secpwd'] == '') {
//                return json(['code' => -2, 'msg' => '未设置二级密码']);
//            }
            try {
                (new MoneyLogic())->carryAdd($this->user);

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $id = $request->param('id', 1, 'intval');
            // $this->assign('bankNames', Bank::getBankNames());
            // $this->assign('bankInfo', UserBank::getUserDefaultBank($this->user_id));
            // $this->assign('moneyNames', get_money_name());
            // $this->assign('info', MoneyCarryModel::where(['id' => $id])->field('id,mid,low,bei,out,fee')->find());
            
            $num = MoneyLog::where('uid',$this->user_id)->where('money',-0.3)->where('is_type',120)->count();
            $numA = MoneyLog::where('uid',$this->user_id)->where('money',-1)->where('is_type',120)->count();
            $numB = MoneyLog::where('uid',$this->user_id)->where('money',-3)->where('is_type',120)->count();
            $numC = MoneyLog::where('uid',$this->user_id)->where('money',-5)->where('is_type',120)->count();
            $assignData = [
                'ConfigNum' => $num,
                'numA' => $numA,
                'numB' => $numB,
                'numC' => $numC,
                'carryConfig' => MoneyCarryBankLog::getCarryConfig()
            ];
            
            return view('carry_money/carry_add', $assignData);
        }
    }

    /**
     * 提现列表
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function carryInfo(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            $this->assign('moneyNames', get_money_name());
            $this->assign('info', MoneyCarryModel::where(['status' => 1])->field('id,mid,low,bei,out,fee')->select());
            return view('carry_money/carry_info');
        }
    }

}
