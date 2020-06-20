<?php

namespace app\wap\controller;

use app\common\logic\ValiDateLogic;
use app\common\logic\AddressLogic;
use app\common\logic\AgentLogic;
use app\common\model\Region;
use app\wap\validate\AddAddress;
use think\Request;
use think\Db;
use app\common\model\Address as AddressModel;
use think\response\Json;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Address extends Base
{

    /**
     * 地址列表
     * @param Request $request
     * @return \think\response\View
     */
    public function addressIndex(Request $request)
    {
        $type = $request->param('type',2,'intval');
        $cartId = $request->param('cart_id',0,'intval');
        if ($request->isAjax()) {
            $type = $request->get('type');
            $cartId = $request->get('cart_id');
            $addressModel = new AddressModel();
            $regionModel = new Region();

            $regionInfo = $regionModel->getRegionName();
            $addInfo = $addressModel->getUsersAddressInfoById();
            return view('address/address_index_ajax',['addInfo'=> $addInfo,'regionInfo'=>$regionInfo,'type' => $type,'cartId'=>$cartId]);
        } else {
            return view('address/address_index',['uid'=>$this->user_id,'type' => $type,'cartId'=>$cartId]);
        }
    }

    /**
     * 添加地址
     * @param Request $request
     * @return Json|\think\response\View
     */
    public function addAddress(Request $request)
    {
        $type = $request->param('type',2,'intval');
        $catId = $request->param('cart_id',0,'intval');

        if ($request->isAjax()){
            $data = $request->post();
            $res = $this->validate($data,'app\wap\validate\AddAddress');
            if ($res !== true){
                return json(['code' => -1, 'msg' => $res]);
            }
            $AddressLogic = new AddressLogic();
            try{
                $AddressLogic->addAddress($this->user);
                return json(['code' => 1, 'msg' => '添加成功']);
            }catch (\Exception $e){
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }else{

            return view('address/add_address',['type'=>$type,'catId' =>$catId]);
        }
    }

    /**
     * 删除地址
     * @param Request $request
     * @return Json
     */
    public function delAddress(Request $request)
    {
        if ($request->isPost()){
            $AddressLogic = new AddressLogic();
            try{
                $AddressLogic->delAddress($this->user);
                return json(['code' => 1, 'msg' => '删除成功']);
            }catch (\Exception $e){
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

    /**
     * 默认地址
     * @param Request $request
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setDefaultAddress(Request $request)
    {
        if ($request->isPost()){
            $id = $request->param('id');
            $addressModel = new AddressModel();

            $addressInfo = $addressModel->getUsersAddressById($id);
            if ($addressInfo['default'] == 1){
                return json(['code' => -1, 'msg' => '已经是默认的地址']);
            }
            $res = $addressModel->setDefault($id, $this->user_id);//设置默认地址
            if ($res){
                return json(['code' => 1, 'msg' => '操作成功']);
            }else{
                return json(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 地址修改
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editAddress(Request $request)
    {
        $id = $request->param('id','','intval');
        $type = $request->param('type',2,'intval');
        $catId = $request->param('cart_id',0,'intval');

        if ($request->isAjax()){
            $data = $request->post();
            $res = $this->validate($data,'app\wap\validate\AddAddress');
            if ($res !== true){
                return json(['code' => -1, 'msg' => $res]);
            }
            $AddressLogic = new AddressLogic();
            try{
                $AddressLogic->editAddress($this->user);
                return json(['code' => 1, 'msg' => '修改成功']);
            }catch (\Exception $e){
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }else{
            $addressModel = new AddressModel();
            $regionModel = new Region();

            $regionInfo = $regionModel->getRegionName();
            $addressInfo = $addressModel->getUsersAddressById($id);
            return view('address/edit_address',['addressInfo'=>$addressInfo,'regionInfo'=>$regionInfo,'type' => $type,'catId'=>$catId]);
        }
    }
}
