<?php

namespace app\common\model;

class WebUserMessage extends Common
{
    protected $pk = 'rec_id';
    protected $name = 'web_users_message';

    public static $noticeType = [
        '1' => '管理通知'
    ];

    /**
     * 检查该会员是否收到管理员发的通知
     * @param array $userInfo 会员信息
     * @return int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkWebNotice($userInfo)
    {
        $userMessagList = $this->where('uid', (int)$userInfo['user_id'])->field('message_id')->select();

        $webMessageModel = new WebMessage();

        // 查询会员未读的通知
        $userNoReadNotice = $webMessageModel->where('send_time', '>', $userInfo['reg_time'])
            ->whereNotIn('message_id', get_arr_column($userMessagList, 'message_id'))
            ->field('message_id,send_user_id')->select();

        $userMessageData = [];

        foreach ($userNoReadNotice as $v) {
            $sendUserIdArr = explode(',', $v['send_user_id']);
            if (in_array($userInfo['user_id'], $sendUserIdArr)) {
                $userMessageData[] = [
                    'uid' => $userInfo['user_id'],
                    'message_id' => $v['message_id'],
                    'status' => 1
                ];
            }
        }

        return $this->insertAll($userMessageData);
    }

    /**
     * 获取会员站内通知信息
     * @param array $userInfo 会员信息
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserMessageByUserId($userInfo, $page = 0, $pageSize = 10)
    {
        $this->checkWebNotice($userInfo);

        $list = $this->alias('webUserMessage')
            ->view('WebMessage', 'message,send_time,send_type,message_id', 'webUserMessage.message_id = WebMessage.message_id')
            ->where('uid', $userInfo['user_id'])->limit($page * $pageSize, $pageSize)->field('status')->order('status', 'asc')->select();

        return $list;
    }

    /**
     * 获取会员站内通知详情
     * @param array $userInfo 会员信息
     * @param int $messageId 通知id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMessageInfoById($userInfo, $messageId)
    {
        $info = $this->alias('webUserMessage')
            ->view('WebMessage', 'message,send_time,send_type,message_id', 'webUserMessage.message_id = WebMessage.message_id')
            ->where('uid', $userInfo['user_id'])->where('webUserMessage.message_id', (int)$messageId)->field('rec_id')->find();

        return $info;
    }

    /**
     * 获取会员站内通知未读数
     * @param array $userInfo 会员信息
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserMessageNoReadNumByUser($userInfo)
    {
        $this->checkWebNotice($userInfo);

        $count = $this->alias('webUserMessage')->view('WebMessage', 'message,send_time,send_type,message_id', 'webUserMessage.message_id = WebMessage.message_id')->where('uid', $userInfo['user_id'])->where('status', 1)->count();

        return $count;
    }
}