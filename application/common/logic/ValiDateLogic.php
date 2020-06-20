<?php
namespace app\common\logic;
use think\facade\Request;
use app\common\model\UserBank;
use app\common\model\Users;
use app\common\model\UsersData;
use think\facade\Log;

/**
 * Class ValidateLogic
 * @package app\common\logic
 */
class ValidateLogic
{

    /**
     * 添加银行卡
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addUserBank()
    {
        //接收传值
        $userId = Request::param('user_id','','intVal');
        $bankName = Request::param('bank_name');
        $bankAccount = Request::param('bank_account');
        $openingId = Request::param('opening_id','','intVal');
        $bankAddress = Request::param('bank_address');
        //实例化model类
        $userModel =  new Users();
        $UserBankModel = new UserBank();

        $info  = $userModel->getUserByUserId($userId);
        if (!$info){
            exception('没有会员信息');
        }
        if ($openingId == 0){
            exception('请选择开户银行');
        }
        $userBankInfo = $UserBankModel->getBankByBankInfo($openingId,'bank_account');
        $res = $UserBankModel::where(['uid'=> $userId,'bank_default'=>1])->count('bank_default');
        if ($res ==  1){
            $default = 2;
        }else{
            $default = 1;
        }
        if ($bankAccount == $userBankInfo['bank_account'] ){
            exception('该银行账号已经存在');
        }
        $res = $UserBankModel->addBank($userId,$openingId,$bankAccount,$bankAddress,$bankName,$default);
        if (!$res){
            exception('添加失败');
        }
        return true;
    }

    /**
     * 验证会员银行卡信息
     * @return bool
     * @throws \Exception
     */
    public function validateBank()
    {
        // 接收传值
        $bankName = Request::param('bank_name');
        $bankAccount = Request::param('bank_account');
        $openingId = Request::param('opening_id','','intVal');
        $bankAddress = Request::param('bank_address');
        $Id = Request::param('id','','intVal');
    //    $bankDefault = Request::param('bank_default');

         $UserBankModel = new UserBank();

        $Info = $UserBankModel->getInfoById($Id);
        $bankDefault = $Info['bank_default'];

        if ($Info == ''){
            exception('没有该银行卡信息');
        }
        if ($openingId <= 0){
            exception('请选择开户银行');
        }

//    if (zfCache('securityInfo.bankAppKey') != '') {
//        import('Common.Org.Curl');
//        $data = array(
//            'idcard' => $user['number'],
//            'realname' => $post['bank_name'],
//            'bankcard' => preg_replace('# #', '', $post['bank_account']),
//            'key' => zfCache('securityInfo.bankAppKey')
//        );
//        $curl = new \Curl($data);
//
//        $res = $curl->httpRequest('http://v.juhe.cn/verifybankcard3/query', 1);
//        $res = json_decode($res, true);
//        if ($res['error_code'] != 0) {
//            return array('status' => -1, 'msg' => '银行账号错误');
//        } elseif ($res['result']['res'] != 1) {
//            return array('status' => -1, 'msg' => '银行账号与身份证不匹配');
//        }
//    }
//    return ['status' => 1, 'msg' => '验证成功'];

        $result = $UserBankModel->updateUserBankInfo($Id,$bankName,$bankAddress,$openingId,$bankAccount,$bankDefault);
        if (!$result){
            exception('修改失败');
        }
        return true;
    }

    /**
     * 删除
     * @return bool
     * @throws \Exception
     */
    public function delBank()
    {
        // 接收传值
        $id = Request::param('id','','intVal');
        $UserBankModel = new  UserBank();
        $Info = $UserBankModel->delBank($id);
        if (!$Info){
            exception('删除失败');
        }
        return true;
    }


}