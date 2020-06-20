<?php

namespace app\common\model;

class WebMessage extends Common
{

    protected $name = 'web_message';

    /**
     * 发送站内信
     * @param int|array $userId 会员id
     * @param string $sendContent 发送内容
     * @param int $sendType 通知类型 1管理员通知
     * @return int|string
     */
    public function sendMessage($userId, $sendContent, $sendType = 1)
    {
        $adminUser = new AdminUser();
        $data = [
            'admin_id' => $adminUser->getAdminUserId(),
            'message' => $sendContent,
            'send_user_id' => is_array($userId) ? implode(',', $userId) : $userId,
            'send_type' => $sendType,
            'send_time' => time()
        ];

        return $this->insertGetId($data);
    }

}