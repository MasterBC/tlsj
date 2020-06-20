<?php

namespace app\wap\controller;

use think\Request;
use app\common\model\Notice as NoticeModel;
use app\common\model\auth\AuthGroup;

/**
 * Class Notice
 * @package app\wap\controller
 */
class Notice extends Base
{
    /**
     * 公告中心
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeIndex(Request $request)
    {
        if ($request->isAjax()) {
            $NoticeModel = new NoticeModel();
            $AuthGroupModel = new AuthGroup();

            $noticeInfo = $NoticeModel->getNoticeInfo();
            $typeInfo = $AuthGroupModel->getField('id,title');


            return view('notice/notice_ajax', ['typeInfo' => $typeInfo, 'noticeInfo' => $noticeInfo]);
        }
        return view('notice/notice');

    }

    /**
     * 公告详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeDetails(Request $request)
    {
        $id = $request->param('id', '', 'intval');

        $AuthGroupModel = new AuthGroup();
        $noticeModel = new NoticeModel();

        $notInfo = $noticeModel->getNoticeById($id);
        if (empty($notInfo)) {
            $this->error('非法操作');
        }
        $lastData = $notInfo->getPrevNoticeInfo();
        $nextData = $notInfo->getNextNoticeInfo();
        $typeInfo = $AuthGroupModel->getField('id,title');
        $noticeModel->userReadNotice($notInfo['id'], $this->user['user_id']);

        return view('notice/notice_details', [
            'lastData' => $lastData,
            'nextData' => $nextData,
            'typeInfo' => $typeInfo,
            'notInfo' => $notInfo
        ]);
    }

}