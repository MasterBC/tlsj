<?php
namespace app\common\model;

use think\db\Where;
use think\Model;
use think\facade\Request;
use think\Db;
use think\Log;

class Address extends Model
{
    protected $name = 'user_address';

    /**
     * 添加地址
     * @param $userId 用户id
     * @param $username 收货人
     * @param $mobile 手机号码
     * @param $country 省
     * @param $province 市
     * @param $city 县
     * @param $district 街道
     * @param $twon
     * @param $address 详细地址
     * @param $default 默认地址1 不默认2
     * @return int|string
     */
    public function addAddress($userId,$username,$mobile,$country,$province,$city,$district,$twon,$address,$default = 2)
    {
        $data = [
            'uid' => $userId,
            'username' => $username,
            'mobile' => $mobile,
            'country' => $country,
            'province' =>$province ,
            'city' => $city,
            'district' => $district,
            'twon' => $twon,
            'address' =>$address,
            'default' => $default,
        ];
        return $this->insertGetId($data);
    }

    /**
     * 修改地址
     * @param $id 地址id
     * @param $username 收货人
     * @param $mobile 手机号码
     * @param $country 省
     * @param $province 市
     * @param $city 县
     * @param $district 街道
     * @param $twon
     * @param $address 详细地址
     * @return Address
     */
    public function editAddress($id,$username,$mobile,$country,$province,$city,$district,$twon,$address)
    {
        $data = [
            'username' => $username,
            'mobile' => $mobile,
            'country' => $country,
            'province' =>$province ,
            'city' => $city,
            'district' => $district,
            'twon' => $twon,
            'address' =>$address,
        ];
        return $this->where('id',$id)->update($data);
    }
    /**
     * 查询个人地址
     * @param $userId
     */
    public function getUsersAddressInfoById()
    {
        $where = new Where();
        Request::param('uid') && $where['uid'] = Request::param('uid');
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        $pSize = 8;
        $result = $this->where($where)->limit($p * $pSize, $pSize)->order('default' , 'asc')->select();
        return $result;

    }

    /**
     * 查询默认地址
     * @param $userId
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUsersAddressDefault($userId)
    {
        $where= [
            'uid' => (int)$userId,
            'default' => 1,
        ];
        return $this->where($where)->find();
    }

    /**
     * 查询地址
     * @param $userId
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUsersAddressById($Id)
    {
        $where= [
            'id' => (int)$Id,
        ];
        return $this->where($where)->find();
    }

    /**
     * 设置默认地址
     * @param int $id 银行卡id
     * @param int $userId 会员id
     * @return bool
     */
    public function setDefault($id, $userId)
    {
        $this->where(['uid' => $userId])->setField('default', 2);

        $res = $this->where(['id' => $id])->setField('default', 1);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 删除地址
     * @param $userId 用户id
     * @param $id 地址id
     * @return bool
     * @throws \Exception
     */
    public function delAddress($id,$userId)
    {
        $where = [
            'id'=>(int)$id,
            'uid'=>(int)$userId,
        ];
        return $this->where($where)->delete();
    }
}