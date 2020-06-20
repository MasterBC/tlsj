<?php

namespace app\common\model;

use think\db\Where;
use think\Model;
use think\facade\Request;
use think\facade\Cache;

class Bank extends Model
{
    protected $name = 'bank';


    /**
     * 添加收款方式
     * @return int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addBank()
    {
        if (!$this->checkNameUnique(Request::param('name_cn'))) {
            exception('此银行名称已存在');
        }

        $data = Request::param();
        unset($data['file']);
        $res = $this->allowField([
            'status', 'is_c', 'is_t', 'name_cn', 'username', 'account', 'address', 'logo', 'code'
        ])->insertGetId($data);

        $this->_afterInsert();

        return $res;
    }

    /**
     * 编辑收款方式
     * @return int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editBank()
    {
        if (!$this->checkNameUnique(Request::param('name_cn'), $this->id)) {
            exception('此银行名称已存在');
        }
        $data = Request::param();
        unset($data['file']);

        $res = $this->allowField([
            'status', 'is_c', 'is_t', 'name_cn', 'username', 'account', 'address', 'logo', 'code'
        ])->save($data);

        $this->_afterUpdate();

        return $res;
    }

    /**
     * 检测名称是否唯一
     * @param $name
     * @param int $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkNameUnique($name, $id = 0)
    {
        $info = $this->getInfoByName($name);
        if (empty($info) || $info['id'] == $id) {
            return true;
        }
        return false;
    }

    /**
     * 根据名称获取信息
     * @param $name
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByName($name)
    {
        return $this->where('name_cn', $name)->find();
    }

    /**
     * 查询某个字段
     * @param array $where
     */
    public function getBankInfo($data = [])
    {
        $a = $this->column($data);
        return $a;
    }

    /**
     * 根据条件查询某个字段
     * @param array $where
     */
    public function getBankInfoByField($where = '', $data = [])
    {
        return $this->where($where)->field($data)->select();
    }

    /**
     * 根据id获取数据
     * @param int $id 银行id
     * @param array $field 字段
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function getBankFieldById($id, $field = [])
    {
        $where = [
            'id' => (int)$id
        ];
        if (is_array($field)) {
            $cacheKey = implode('_', $field);
        } else {
            $cacheKey = str_replace(',', '_', $field);
        }
        $cacheKey = 'get_web_bank_info_byId_' . $id . '_' . $cacheKey;
        return $this->where($where)->cache($cacheKey, null, 'web_bank_info')->field($field)->find();
    }

    /**
     * 获取银行卡名称
     */
    public static function getBankNames()
    {
        return self::where(['status' => 1])->cache('get_bank_names', null, 'web_bank_info')->column('id,name_cn');
    }

    /**
     * 获取充值银行
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRechargeBank()
    {
        $list = self::where('status', 1)->where('is_c', 1)->cache('get_recharge_bank_info', null, 'web_bank_info')->select();

        return $list;
    }

    /**
     * 获取提现银行
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCarryBank()
    {
        $list = self::where('status', 1)->where('is_t', 1)->cache('get_carry_bank_info', null, 'web_bank_info')->select();

        return $list;
    }

    /**
     * 添加后操作
     */
    public function _afterInsert()
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
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::clear('web_bank_info');
    }

}
