<?php

namespace app\common\model;

use think\Model;

class UsersData extends Model
{
    protected $name = 'users_data';

    /**
     * 插入数据
     * @param  array $userData 数据
     * @return int|string 插入数据的id
     */
    public function addUserDataInfo($userData)
    {
        return $this->insertGetId($userData);
    }

    /**
     * 根据条件查出数据
     * @param string $where 条件
     * @param  int $type 类型
     * @param  array $field 要查的字段
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserDataInfo($where, $type, $field = [])
    {
        switch ($type) {
            case 1:
                $where = ['id' => $where];
                break;
            case 2:
                $where = ['mobile' => $where];
                break;
        }

        return $this->where($where)->field($field)->find();
    }

    /**
     * 根据条件查出数据
     * @param $id 用户的 data_id
     * @param $field 字段
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function  getUserDataField($id, $field = '')
    {
        $where = [
            'id' => (int)$id
        ];
        return $this->where($where)->field($field)->find();
    }

    /**
     * 根据手机号查询会员数量
     * @param array $field
     * @return float|string
     */
    public function getUserCountByMobile($field = [])
    {
        $where = [
            'mobile' => $field
        ];

        return $this->where($where)->count();
    }

    /**根据id 获取数据字段
     * @param array $Id 用户data_id
     * @param string $field 字段
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTjrInfo($Id = [] ,$field = '')
    {
        if ($field != ''){
            return $this->whereIn('id',$Id)->column($field);
        }
    }

    /**修改用户个人中心数据
     * @param $data
     * @return UsersData
     */
    public function updateUserData($Id = '',$data= [])
    {
        $where = [
            'id'=>(int) $Id
        ];

        return $this->where($where)->update($data);
    }
}
