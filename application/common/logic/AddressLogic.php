<?php
namespace app\common\logic;

use app\common\model\Address;
use think\facade\Request;
use think\Db;
use think\Model;
use think\facade\Log;

class AddressLogic
{
    /**
     * 添加收货地址
     * @param $userInfo
     * @return bool
     * @throws \Exception
     */
    public function addAddress($userInfo)
    {
        // 将传过来的值强制转换
        $province = Request::param('province', '', 'floatval');
        $city = Request::param('city', '', 'floatval');
        $district = Request::param('district', '', 'floatval');
        $twon = Request::param('twon', '', 'floatval');
        $address = Request::param('address');
        $mobile = Request::param('mobile');
        $username = Request::param('username');
        $id = Request::param('id', '', 'intval');
        $userId = $userInfo['user_id'];
        if (!$userId){
            exception('网络错误，请稍后再试');
        }
        if($province <= 0 || $city <= 0 || $district <= 0) {
            exception('请选择收货地址');
        }
        $num = Address::where('uid',$userId)->count();
        if ($num >= 10){
            exception('收货地址已达上限');
        }
        //实例化model类
        $addressModel = new Address();

         $defaultInfo = $addressModel->getUsersAddressDefault($userId);//查询默认地址
        //为空就是默认地址
        if ($defaultInfo == ''){
            $default = 1;
        }else{
            $default = 2;
        }
        $country = 0;//国家默认为0
       $res = $addressModel->addAddress($userId,$username,$mobile,$country,$province,$city,$district,$twon,$address,$default);//添加地址
        if (!$res){
            exception('添加失败');
        }
        if ($id == 1 && $default == 2){
            $addressModel->setDefault($res,$userId);//用户二次添加地址如果想要设为默认
        }
        return true;
    }

    /**
     * 修改地址
     * @param $userInfo
     * @return bool
     * @throws \Exception
     */
    public function editAddress($userInfo)
    {
        // 将传过来的值强制转换
        $province = Request::param('province', '', 'floatval');
        $city = Request::param('city', '', 'floatval');
        $district = Request::param('district', '', 'floatval');
        $twon = Request::param('twon', '', 'floatval');
        $address = Request::param('address');
        $mobile = Request::param('mobile');
        $username = Request::param('username');
        $id = Request::param('id', '', 'intval');
        $userId = $userInfo['user_id'];
        if (!$userId){
            exception('网络错误，请稍后再试');
        }
        if($province <= 0 || $city <= 0 || $district <= 0) {
            exception('请选择收货地址');
        }
        $country = 0;//国家默认为0
        //实例化model类
        $addressModel = new Address();

       $res = $addressModel->editAddress($id,$username,$mobile,$country,$province,$city,$district,$twon,$address);
        if (!$res){
            exception('修改失败');
        }
        return true;
    }

    /**
     * 删除地址
     * @param $userInfo
     * @return bool
     * @throws \Exception
     */
    public function delAddress($userInfo)
    {
        $addressId = Request::param('id', '', 'intval');
        $userId = $userInfo['user_id'];
        if (!$userId){
            exception('网络错误，请稍后再试');
        }
        $addressModel = new Address();

        $count = $addressModel::where('uid',$userId)->count();
        if ($count == 1){
            exception('请至少保留一条收货信息');
        }
         $res = $addressModel->delAddress($addressId,$userId);
         if (!$res){
             exception('删除失败');
         }
         return true;
    }
}