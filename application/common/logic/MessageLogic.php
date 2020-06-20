<?php

namespace app\common\logic;
use app\common\model\Message as MessageModel;
use app\common\model\Users;
use think\facade\Session;
use think\facade\Request;

class MessageLogic
{
    /**
     * 添加留言
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addMessageIndex($userInfo)
    {
        //获取参数
        $title = Request::param('title');
        $type = Request::param('type');
        $cont = Request::param('content');
        $userId = $userInfo['user_id'];
        $img = '';
        foreach(Request::param('img') as $v) {
            if($v) {
                $img .= $v.',';
            }
        }
        if ($type == 0){
            exception('请选择类型');
        }
        $thumb = trim($img,',');

        //实例化model类
        $MessageModel = new MessageModel();
         $res = $MessageModel->addMessageData($userId,$title,$type,$cont,$thumb);
         if (!$res){
             exception('添加失败');
         }
         return true;
    }
}
