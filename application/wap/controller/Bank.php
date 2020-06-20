<?php
namespace app\wap\controller;
use think\Request;
use app\common\model\UserBank;
use app\common\model\Bank as BankModel;
use think\db;
use app\common\logic\ValiDateLogic;

class Bank extends Base
{

    /**
     * 银行卡首页
     * @param Request $request
     * @return \think\response\View
     */
	public function userBank()
	{
        $BankModel = new BankModel();
        $UserBankModel = new UserBank();

        $bank = $UserBankModel->getUserBank($this->user_id);
        $banks = $BankModel->getBankInfo('id,name_cn');
        return view('bank/user_bank',['bank'=>$bank,'banks'=>$banks]);
	}

    /**
     * 会员设置默认银行卡
     * @param Request $request
     * @return \think\response\Json
     */
    public function setDefaultBank(Request $request)
    {
        if ($request->isPost()) {
            $UserBankModel = new UserBank();
            $id = $request->param('id', '', 'intVal');
            $info = $UserBankModel->getInfoById($id);
            if (!$info) {
                return json(['code' => -1, 'msg' => '没有该银行卡的数据']);
            }
            if ($info['bank_default'] == 1) {
                return json(['code' => -1, 'msg' => '已经默认的银行卡']);
            }
            $res = $UserBankModel->setDefault($id, $this->user_id);
            if ($res) {
                return json(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json(['code' => -1, 'msg' => '操作失败']);
            }
        }

    }

    /**
     * 添加银行卡
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function addBank(Request $request)
    {
        $BankModel = new BankModel();
        $where = ['status' =>1];
        $where = ['is_t' =>1];
        $bankName = $BankModel->getBankInfoByField($where,'id,name_cn');
        if ($request->isPost()){
            $data = $request->post();
            $data['bank_account'] = preg_replace('# #', '', $data['bank_account']);
            $res = $this->validate($data,'app\wap\validate\AddBank');
            if ($res !== true){
                return json(['code' => -1, 'msg' => $res]);
            }
            $validateLogic = new ValidateLogic();
            try {
                $validateLogic->addUserBank();
                return json(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }else{
            return view('bank/add_bank',['bankName'=>$bankName,'user_id'=>$this->user_id]);
        }
    }

    /**
     * 修改银行卡
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editBank(Request $request)
    {
        $id = $request->param('id', '', 'intVal');
        $UserBankModel = new UserBank();
        $userBank = $UserBankModel->getInfoById($id);
        if ($request->isPost()) {
            $data = $request->post();
            $data['bank_account'] = preg_replace('# #', '', $data['bank_account']);
            $result = $this->validate($data, 'app\wap\validate\SaveBank');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            $validateLogic = new ValidateLogic();
            try {
                $validateLogic->validateBank();
                return json(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }else{
            $BankModel = new BankModel();
            $where = ['status'=>1, 'is_t'=>1];
            $bankInfo = $BankModel->getBankInfoByField($where,'id,name_cn');

            return view('bank/set_bank_card',['banks'=>$bankInfo,'userBank'=>$userBank]);
        }
    }

    /**
     * 删除银行卡
     * @param Request $request
     * @return \think\response\Json
     */
    public function delBank(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intVal');
            $UserBankModel = new UserBank();
            $bankInfo = $UserBankModel->getInfoById($id);
            if (!$bankInfo) {
                return json(['code' => -1, 'msg' => '未获取到银行信息']);
            }
            $validateLogic = new ValidateLogic();
            try {
                $validateLogic->delBank();
                return json(['code' => 1, 'msg' => '删除成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

}
