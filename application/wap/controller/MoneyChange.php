<?php

namespace app\wap\controller;

use app\common\model\Users;
use app\common\model\money\UsersMoney;
use app\common\model\money\MoneyChange as MoneyChangeModel;
use app\common\model\money\MoneyChangeLog;
use app\common\model\UsersData;
use app\common\model\money\Money as MoneyModel;
use app\common\logic\MoneyLogic;
use think\Request;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class MoneyChange extends Base
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
     * 转账日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexChange(Request $request)
    {
        if ($request->isAjax()) {
            $moneyChangeLogModel = new MoneyChangeLog();

            $moneyChangeLogInfo = $moneyChangeLogModel->getMoneyChangeLog($this->user_id, $this->user_id);
            $this->assign('moneyChangeLogInfo', $moneyChangeLogInfo);
            return view('change_money/change_index_ajax');
        } else {
            $this->assign('mid', $request->param('mid'));
            return view('change_money/change_index');
        }
    }

    /**
     * 钱包转账详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeDetail(Request $request)
    {
        $id = $request->param('id');
        $usersModel = new Users();
        $userDataModel = new UsersData();
        $moneyChangeLogModel = new MoneyChangeLog();

        $logInfo = $moneyChangeLogModel->getMoneyChangeLogInfoById($id);
        $toUsersInfo = $usersModel->getUserByUserId($logInfo['to_uid'], 'account');
        $userInfo = $userDataModel->getUserDataInfo($this->user['data_id'], 1, 'head');
        $this->assign('logInfo', $logInfo);
        $this->assign('userInfo', $userInfo);
        $this->assign('toUsersInfo', $toUsersInfo);
        return view('change_money/change_detail');
    }

    /**
     * 转账申请
     * @param Request $request
     * @return type
     */
    public function changeAdd(Request $request)
    {
        if ($request->isAjax()) {
            if ($this->user['secpwd'] == '') {
                return json(['code' => -2, 'msg' => '未设置二级密码']);
            }
            $data = $request->post();
            $MoneyLogic = new MoneyLogic();

            $result = $this->validate($data, 'app\wap\validate\ChangeMoney');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {
                $MoneyLogic->doMoneyChange($this->user);
                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $mId = $request->param('money_id');
            $uId = $this->user_id;
            //实例化model类
            $userMoney = UsersMoney::getUsersMoneyByUserId($uId, $mId, 1);

            $this->assign('moneyId', $mId);
            $this->assign('userMoney', $userMoney);
            return view('change_money/change_add');
        }
    }

    /**
     * 钱包转账列表
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeInfo()
    {
        $moneyChangeModel = new MoneyChangeModel();

        $userMoneyInfo = $moneyChangeModel->getChangeMoneyField('mid,low,bei,out,fee');

        $moneyNames = get_money_name();
        $this->assign('moneyNames', $moneyNames);
        $this->assign('userMoneyInfo', $userMoneyInfo);
        return view('change_money/change_info');

    }

    /**
     * 转入日志
     * @param Request $request
     * @return type
     */
    public function toChange(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('change_money/to_change');
        }
    }

    /**
     * 转出日志
     * @param Request $request
     * @return type
     */
    public function changeOut(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('change_money/change_out');
        }
    }

}
