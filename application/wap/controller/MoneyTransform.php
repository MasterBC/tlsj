<?php

namespace app\wap\controller;

use app\common\model\money\MoneyTransformLog;
use app\common\model\Users;
use app\common\model\UsersData;
use app\common\model\money\UsersMoney;
use think\Request;
use think\Db;
use app\common\model\money\MoneyTransform as MoneyTransformModel;
use app\common\model\money\Money;
use app\common\logic\MoneyTransformLogic;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class MoneyTransform extends Base
{

    /**
     * MoneyChange constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assign('moneyNames', get_money_name());
    }

    /**
     * 钱包转换日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexTransform(Request $request)
    {
        $mid = $request->param('mid');
        if ($request->isAjax()) {

            $moneyTransformLogModel = new MoneyTransformLog();

            $moneyTransformInfo = $moneyTransformLogModel->getMoneyTransformLog($this->user_id);
            $this->assign('uid', $this->user_id);
            $this->assign('moneyTransformInfo', $moneyTransformInfo);
            return view('transform_money/transform_index_ajax');
        } else {
            $this->assign('mid', $mid);
            return view('transform_money/transform_index');
        }
    }

    /**
     * 转换详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformDetail(Request $request)
    {
        $id = $request->param('id');
        $userDataModel = new UsersData();
        $moneyTransformLogModel = new MoneyTransformLog();

        $logInfo = $moneyTransformLogModel->getTransferLogInfoById($id);
        $userInfo = $userDataModel->getUserDataInfo($this->user['data_id'], 1, 'head');
        $this->assign('userInfo', $userInfo);
        $this->assign('logInfo', $logInfo);
        return view('transform_money/transform_detail');
    }

    /**
     * 钱包金额转换
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function transformAdd(Request $request)
    {
        if ($request->isAjax()) {
            $data = $request->post();
            $result = $this->validate($data, 'app\wap\validate\MoneyTransform');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            //实例化model类
            $moneyTransformLogic = new MoneyTransformLogic();
            try {
                $moneyTransformLogic->doMoneyTransform($data, $this->user);
                return json(['code' => 1, 'msg' => '转换成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {

            $uid = $this->user_id;
            $mid = $request->param('mid');
            $toMid = $request->param('to_mid');
            $midMoney = UsersMoney::getUsersMoneyByUserId($uid, $mid, 1);

            $this->assign('mid', $mid);
            $this->assign('toMid', $toMid);
            $this->assign('midMoney', $midMoney);
            return view('transform_money/transform_add');
        }
    }

    /**
     * 钱包转换列表页
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformInfo(Request $request)
    {
        $moneyTransformModel = new MoneyTransformModel();

        $moneyTransformInfo = $moneyTransformModel->getMoneyTransformInfo();
        $this->assign('moneyTransformInfo', $moneyTransformInfo);
        return view('transform_money/transform_info');
    }

}
