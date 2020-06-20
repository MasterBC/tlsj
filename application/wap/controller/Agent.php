<?php
namespace app\wap\controller;

use think\Request;
use app\common\model\Users;
use think\Db;
/**
 * Class Agent
 * @package app\wap\controller
 */
class Agent extends Base
{
    public function bdrIndex(Request $request)
    {
        if ($request->isAjax()){
            $userId = $request->param('user_id');
            $userId = $userId > 0 ? $userId : $this->user_id;
            $tjrId = $userId;
            $userInfo = Users::getTjrIndex($tjrId);
            $tjrNum = [];
            foreach($userInfo as $v){
                $tjrNum[$v['user_id']] = Users::getTjrUserNum($v['user_id']);
            }
            $this->assign('num',$tjrNum);
            $this->assign('userInfo',$userInfo);
            return view('agent/bdr_index_ajax');
        }else{
            return view('agent/bdr_index');
        }
    }


}