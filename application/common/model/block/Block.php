<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Log;
use think\facade\Cache;

class Block extends Model
{
    protected $name = 'block';

    /**
     * 根据条件查询数据
     * @param array $where
     * @param int $type
     * @param array $field
     * @return array|null|\PDOStatement|string|\think\Collection|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBlockInfo($where, $type = 1, $field = [])
    {
        switch ($type) {
            case 1:
                $info = $this->where($where)->field($field)->find();
                break;
            case 2:
                $info = $this->where($where)->field($field)->select();
                break;
        }
        return $info;
    }

    /**
     * 获取字段信息
     * @param array $field
     * @return array
     */
    public static function getBlockInfoField($field = [])
    {
        $where = [
            'status' => 1
        ];
        if (is_array($field)) {
            $cacheKey = implode('_', $field);
        } else {
            $cacheKey = str_replace(',', '_', $field);
        }
        $cacheKey = $cacheKey . '_field';
        return self::where($where)->cache($cacheKey, null, 'all_block_info')->column($field);
    }

    /**
     * 根据用户添加货币钱包
     * @param $userId 用户的id
     * @return bool
     */
    public function addUserBlock($userId)
    {
        try {
            $list = $this->field('id')->select();
            $userBlocks = UsersBlock::where('uid', $userId)->column('uid', 'bid');
            foreach ($list as $v) {
                if (!isset($userBlocks[$v['id']])) {
                    $data = [
                        'bid' => $v['id'],
                        'uid' => $userId,
                        'address' => md5(time() . $v['id']),
                        'update_time' => time(),
                    ];
                    UsersBlock::insert($data);
                }
            }
        } catch (\Exception $e) {
            Log::write('添加用户货币钱包失败 会员id' . $userId . ': ' . $e->getMessage(), 'error');
        }

        return true;
    }

    /**
     * 获取货币信息
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBlockInfoById($id)
    {
        return self::where('id', $id)->cache('block_info_' . $id)->find();
    }

    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 获取钱包名称
     * @return array
     */
    public static function getBlockNames()
    {
        return self::cache('block_log_type_cn')->column('name_cn', 'id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('block_log_type_cn');
        if (isset($this->id)) {
            Cache::rm('block_info_' . $this->id);
        }
        Cache::clear('all_block_info');
    }
}
