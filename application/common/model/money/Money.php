<?php

namespace app\common\model\money;

use think\Model;
use think\facade\Log;
use think\facade\Cache;

class Money extends Model
{

    protected $pk = 'money_id';

    protected $name = "money";

    public static $epTradeMoneyIds = [2, 3, 4, 5];

    /**
     * 获取字段信息
     * @param string|array $field
     * @return array
     */
    public function getMoneyInfoField($field = [])
    {
        $where = [
            'status' => 1
        ];
        if (is_array($field)) {
            $cacheKey = implode('_', $field);
        } else {
            $cacheKey = str_replace(',', '_', $field);
        }
        return $this->where($where)->cache($cacheKey, null, 'get_money_info_field')->column($field);
    }

    /**
     * 根据id查出钱包信息
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMoneyInfoById($id)
    {
        $id = (int)$id;
        return self::where('money_id', $id)->cache('money_info_' . $id)->find();
    }

    /**
     * 获取参与交易的钱包
     */
    public static function getEpTradeMoneys()
    {
        $moneys = Cache::remember('ep_trade_moneys', function () {
            return self::whereIn('money_id', self::$epTradeMoneyIds)->column('name_cn,logo,money_id', 'money_id');
        });

        return $moneys;
    }

    /**
     * 根据Mid  获取字段
     * @param int $moneyId 钱包id
     * @param string|array $field 字段
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMoneyByMid($moneyId = 0, $field = [])
    {
        $moneyId = (int)$moneyId;
        $where = [
            'money_id' => $moneyId
        ];
        if (is_array($field)) {
            $cacheKey = implode('_', $field);
        } else {
            $cacheKey = str_replace(',', '_', $field);
        }
        return self::where($where)->cache($cacheKey, null, 'get_money_info_field_byid_' . $moneyId)->field($field)->find();
    }

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
    public function getMoneyInfo($where, $type = 1, $field = [])
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
     * 根据类型获取字段数据
     * @param string $Mid id 为数组
     * @param array $field 字段
     * @return array
     */
    public function getMoneyInfoByType($Mid = '', $type = 0)
    {
        $where = [
            'money_id' => (int)$Mid
        ];
        switch ($type) {
            case 1:
                return $this->where($where)->value('money_id');
                break;
            case 2:
                return $this->where($where)->value('logo');
                break;
            case 3:
                return $this->where($where)->value('name_cn');
                break;
        }
    }

    /**
     * 添加用于钱包
     * @param $userId
     * @return bool
     */
    public function addUserMoney($userId)
    {
        try {
            $list = $this->field('money_id')->select();
            $userMoneys = UsersMoney::where('uid', $userId)->column('uid', 'mid');
            foreach ($list as $v) {
                if (!isset($userMoneys[$v['money_id']])) {
                    $data = [
                        'mid' => $v['money_id'],
                        'uid' => $userId
                    ];
                    UsersMoney::insert($data);
                }
            }
        } catch (\Exception $e) {
            Log::write('添加用户钱包失败 会员id' . $userId . ': ' . $e->getMessage(), 'error');
        }
        return true;
    }

    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**获取钱包某个字段的所有信息
     * @param array $field
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyNameInfo($field = [])
    {
        return $this->field($field)->select();
    }

    /**
     * 获取钱包名称
     * @return array
     */
    public static function getMoneyNames()
    {
        return self::cache('money_name_cn')->column('name_cn', 'money_id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('money_name_cn');
        if (isset($this->money_id)) {
            Cache::rm('money_info_' . $this->money_id);
            Cache::clear('get_money_info_field_byid_' . $this->money_id);
        }

        Cache::clear('get_money_info_field');
    }
}