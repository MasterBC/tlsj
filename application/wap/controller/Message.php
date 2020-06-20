<?php

namespace app\wap\controller;

use app\common\logic\MessageLogic;
use app\common\model\WebUserMessage;
use think\Request;
use think\Db;
use app\common\model\Message as MessageModel;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Message extends Base
{

    /**
     *    留言列表
     * @param Request $request
     * @return type
     */
    public function messageIndex(Request $request)
    {
        if ($request->isAjax()) {
            $data = $request->get();
            $res = $this->validate($data, 'app\wap\validate\AddMessage');
            if ($res !== true) {
                return json(['code' => -1, 'msg' => $res]);
            }
            $MessageLogic = new MessageLogic();
            try {
                $MessageLogic->addMessageIndex($this->user);
                return json(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $messageCate = get_message_type();
            return view('message/message_index', ['messageCate' => $messageCate]);
        }
    }

    /**
     *    留言详情
     * @param Request $request
     * @return type
     */
    public function messageDetail(Request $request)
    {
        if ($request->isAjax()) {
            $MessageModel = new MessageModel();

            $messageInfo = $MessageModel->getMessageListByUid($this->user_id);
            $messageCate = get_message_type();
            return view('message/message_detail_ajax', ['messageInfo' => $messageInfo, 'messageCate' => $messageCate]);
        } else {
            return view('message/message_detail');
        }
    }

    /**
     *    删除留言
     * @param Request $request
     * @return type
     */
    public function delMessage(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('message/message_detail');
        }
    }

    /**
     *    提交留言
     * @param Request $request
     * @return type
     */
    public function sendMessage(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('message/message_index');
        }
    }

    /**
     * 系统通知
     * @param Request $request
     * @param WebUserMessage $webUserMessageModel
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function webNotice(Request $request, WebUserMessage $webUserMessageModel)
    {
        if ($request->isAjax()) {

            $list = $webUserMessageModel->getUserMessageByUserId($this->user, $request->param('p', '0', 'intval'), $request->param('size', '10', 'intval'));

            $assignData = [
                'list' => $list,
                'noticeType' => WebUserMessage::$noticeType
            ];

            return view('message/web_notice_ajax', $assignData);

        } else {
            return view('message/web_notice');
        }
    }

    /**
     * 通知详情
     * @param Request $request
     * @param WebUserMessage $webUserMessageModel
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeDetail(Request $request, WebUserMessage $webUserMessageModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $webUserMessageModel->getMessageInfoById($this->user, $id);
        if (empty($info)) {
            $this->error('未获取到通知信息');
        }
        $assignData = [
            'info' => $info,
            'noticeType' => WebUserMessage::$noticeType
        ];
        $info->status = 2;
        $info->save();

        return view('message/notice_detail', $assignData);
    }

    /**
     * 获取消息通知数
     * @param Request $request
     * @param WebUserMessage $webUserMessageModel
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNoticeNum(Request $request, WebUserMessage $webUserMessageModel)
    {
        if ($request->isAjax()) {
            $data = [
                'code' => 1,
                'num' => $webUserMessageModel->getUserMessageNoReadNumByUser($this->user)
            ];

            return json()->data($data);
        }
    }

}
