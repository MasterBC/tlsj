<?php

namespace app\common\model;

use think\db\Where;
use think\Model;
use think\facade\Request;

class Message extends Model
{
    protected $name = "users_message";

    /**
     * 获取未回复留言数量
     * @return float|string
     */
    public function getUnreadQuantity()
    {
        return $this->where('status', 1)->count();
    }

    /**
     * 根据id获取留言信息
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMessageInfoById($id)
    {
        return $this->where('id', (int)$id)->find();
    }


    /** 获取用户留言信息
     * @param string $uid 用户id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMessageListByUid($uid)
    {
        $where = new Where;
        $where['uid'] = (int)$uid;
        $p = Request::param('p', 0, 'intval');
        $pSize = Request::param('size', 8, 'intval');
        $messageInfo = $this->where($where->enclose())->limit($p * $pSize, $pSize)->order('add_time desc')->select();
        return $messageInfo;
    }

    /**
     * 添加留言
     * @param $userId 用户id
     * @param $title 留言标题
     * @param $type 类型
     * @param $content 内容
     * @param $thumb 图片
     */
    public function addMessageData($userId, $title, $type, $content, $thumb = '', $status = 1)
    {
        //封装数据
        $data = [
            'uid' => (int)$userId
            , 'title' => $title
            , 'type' => (int)$type
            , 'content' => $content
            , 'thumb' => $thumb
            , 'add_time' => time()
            , 'status' => $status //未回复
        ];
        return $this->insertGetId($data);
    }
}