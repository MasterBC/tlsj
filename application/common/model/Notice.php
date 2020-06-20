<?php

namespace app\common\model;

use think\db\Where;
use think\Model;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Log;

class Notice extends Model
{

    protected $name = "notice";

    /**
     * 获取会员未读的公告
     *
     * @param array $userInfo 会员信息
     * @return void
     */
    public function getUserUnreadNoticeList($userInfo)
    {
        $dir = __DIR__ . '/notice_read';
        if (!is_dir($dir)) {
            mkdir($dir, 07777);
        }
        $file = $dir . '/' . $userInfo['user_id'] . '.log';
        $str = '';
        try {
            $str = file_get_contents($file);
        } catch (\Exception $e) {
            Log::write('获取会员查看的公告日志失败: user_id:' . $userInfo['user_id'] . ', error_msg: ' . $e->getMessage(), 'error');
        }

        $readNoticeId = (array) json_decode($str, true);

        $noticeList = $this->where('status', 1)
                ->whereNotIn('id', $readNoticeId)
                ->order('top asc,add_time desc')
                ->field('id,content,title')
                ->select();
        foreach ($noticeList as $k => $v) {
            $noticeList[$k]['content'] = strip_tags(htmlspecialchars_decode($v['content']));
        }
        return $noticeList;
    }

    /**
     * 会员阅读公告
     *
     * @param [type] $noticeId
     * @param [type] $userId
     * @return void
     */
    public function userReadNotice($noticeId, $userId)
    {
        $dir = __DIR__ . '/notice_read';
        if (!is_dir($dir)) {
            mkdir($dir, 07777);
        }
        $file = $dir . '/' . $userId . '.log';
        $str = '';
        try {
            $str = file_get_contents($file);
        } catch (\Exception $e) {
            Log::write('获取会员查看的公告日志失败: user_id:' . $userId . ', error_msg: ' . $e->getMessage(), 'error');
        }

        $readNoticeId = (array) json_decode($str, true);
        $readNoticeId[$noticeId] = $noticeId;

        file_put_contents($file, json_encode($readNoticeId));
    }

    /**
     * 获取公告数据
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNoticeInfo()
    {
        $where = [
            'status' => 1
        ];
        $p = intval(Request::param('p'));
        $pSize = 8;
        $info = $this->where($where)->limit($p * $pSize, $pSize)->order('top asc,add_time desc')->select();
        return $info;
    }

    /**
     * 根据id 查询数据
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNoticeById($id)
    {
        $where = [
            'id' => (int) $id
        ];
        return self::where($where)->cache('get_notice_info_byId_' . $id)->find();
    }

    /**
     * 获取上一篇数据
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPrevNoticeInfo()
    {
        $info = $this->where('id', '<', $this->id)->cache('get_prev_notice_info_byId_' . $this->id, null, 'prev_notices')->order('id desc')->limit(1)->find();

        return $info;
    }

    /**
     * 获取下一篇数据
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNextNoticeInfo()
    {
        $info = $this->where('id', '>', $this->id)->cache('get_next_notice_info_byId_' . $this->id, null, 'next_notices')->order('id desc')->limit(1)->find();

        return $info;
    }

    /**
     * 获取置顶公告
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getTopNoticeList()
    {
        return self::where('top', 1)->cache('get_top_notice_list')->select();
    }

    /**
     * 添加后操作
     */
    public function _afterInsert()
    {
        $this->clearCache();
    }

    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 删除后操作
     */
    public function _afterDelete()
    {
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::clear('next_notices');
        Cache::clear('prev_notices');
        if (isset($this->id)) {
            Cache::rm('get_notice_info_byId_' . $this->id);
        }
    }

}
