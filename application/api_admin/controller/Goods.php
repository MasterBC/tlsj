<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\goods\GoodsSpecItem;
use app\common\model\goods\GoodsSpecPrice;
use think\facade\Cache;
use think\Request;
use app\common\model\goods\Goods as GoodsModel;
use app\common\model\goods\GoodsCate;
use app\common\model\goods\GoodsSpec;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use think\db;

class Goods extends Base
{
    /**
     * 商品分类列表
     * @param Request $request
     * @param Where $where
     * @param GoodsCate $goodsCateModel
     * @return \think\response\Json|\think\response\View
     */
    public function goodsCateList(Request $request, Where $where, GoodsCate $goodsCateModel)
    {
        $topLevelName = $goodsCateModel->getTopLevelNames();
        if ($request->param('is_get_data') == true) {
            try {
                // 分类名称搜索
                if ($name = $request->param('name', '', 'trim')) {
                    $where['name'] = ['like', '%' . $name . '%'];
                }
                // 上级分类查询
                $parentId = $request->param('parent_id');;
                if ($parentId != '') {
                    $where['parent_id'] = (int)$parentId;
                }

                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = $goodsCateModel->where($where)->limit($page * $pageSize, $pageSize)->order('cat_id', 'desc')->select();

                $cateList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['cat_id'],
                        'name' => $v['name'],
                        'is_top' => $v['is_top'] ? '是' : '否',
                        'status' => $v['status'] == 1 ? '显示' : '关闭',
                        'sort' => $v['sort'],
                        'parent_cate_name' => $v['parent_id'] > 0 ? $topLevelName[$v['parent_id']] : '顶级分类'
                    ];

                    $cateList[] = $arr;
                }

                if (empty($cateList)) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '未查询到分类'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $cateList,
                        'count' => $goodsCateModel->where($where)->count()
                    ];
                }

                return json()->data($data);
            } catch (\Exception $e) {

                Log::write('查询商品分类失败： ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '查询商品分类失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'topLevelName' => $topLevelName
            ]);
        }
    }

    /**
     * 修改商品分类
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsSpec $goodsSpecModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editGoodsCate(Request $request, GoodsCate $goodsCateModel, GoodsSpec $goodsSpecModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $goodsCateModel->getCateInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到分类信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'name', 'sort', 'status', 'parent_id', 'spec_key'
                ])->save($request->param());
                $info->_afterUpdate();
                AdminLog::addLog('修改商品分类', $request->param(), $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            } catch (\Exception $e) {
                Log::write('修改商品分类失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
        } else {
            $info['spec_key'] = explode(',', $info['spec_key']);
            $assignData = [
                'info' => $info,
                'topLevelNames' => $goodsCateModel->getTopLevelNames(),
                'goodsSpec' => $goodsSpecModel->getAdminSortSpec()
            ];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
        }
    }

    /**
     * 添加商品分类
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsSpec $goodsSpecModel
     * @return \think\response\Json|\think\response\View
     */
    public function addGoodsCate(Request $request, GoodsCate $goodsCateModel, GoodsSpec $goodsSpecModel)
    {
        if ($request->isPost()) {
            try {
                $goodsCateModel->allowField([
                    'name', 'sort', 'status', 'parent_id', 'spec_key'
                ])->save($request->param());
                $goodsCateModel->_afterInsert();
                AdminLog::addLog('添加商品分类', $request->param(), $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
            } catch (\Exception $e) {
                Log::write('添加商品分类失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
            }
        } else {
            $assignData = [
                'topLevelNames' => $goodsCateModel->getTopLevelNames(),
                'goodsSpec' => $goodsSpecModel->getAdminSortSpec()
            ];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
        }
    }

    /**
     * 删除商品分类
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @return \think\response\Json
     */
    public function delGoodsCate(Request $request, GoodsCate $goodsCateModel)
    {
        try {
            $id = $request->param('id', '', 'intval');
            $info = $goodsCateModel->getCateInfoById($id);
            if (empty($info)) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到分类信息');
            }
            if ($info->getSubCateNum() > 0) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '该分类下面存在下级分类，请先处理后重试');
            }
            $info->delete();
            $info->_afterDelete();
            AdminLog::addLog('删除商品分类', $info->toArray(), $this->adminUser['admin_id']);
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
        } catch (\Exception $e) {
            Log::write('删除商品分类失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
        }

    }

    /**
     * 商品列表
     * @param Request $request
     * @param Where $where
     * @param GoodsModel $goodsModel
     * @return \think\response\Json|\think\response\View
     */
    public function goodsList(Request $request, Where $where, GoodsModel $goodsModel, GoodsCate $goodsCateModel)
    {
        $cateNames = $goodsCateModel->getCateName();
        if ($request->param('is_get_data') == true) {
            try {

                if ($goodsName = $request->param('goods_name', '', 'trim')) {
                    $where['goods_name'] = ['like', '%' . $goodsName . '%'];
                }
                if ($cateId = $request->param('cate_id', '', 'intval')) {
                    $where['cat_id'] = $cateId;
                }

                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = $goodsModel->where($where)->limit($page * $pageSize, $pageSize)->order('goods_id', 'desc')->select();

                $goodsList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['goods_id'],
                        'cate_name' => $cateNames[$v['cat_id']] ?? '',
                        'goods_name' => $v['goods_name'],
                        'picture' => get_img_domain() . $v['picture'],
                        'shop_price' => $v['shop_price'],
                        'goods_pv' => $v['goods_pv'],
                        'stock' => $v['stock'],
                        'is_top' => $v['is_top'] == 1 ? '是' : '否',
                        'is_hot' => $v['is_hot'] == 1 ? '是' : '否',
                        'is_new' => $v['is_new'] == 1 ? '是' : '否',
                        'status' => $v['status'] == 1 ? '开启' : '关闭',
                        'sort' => $v['sort']
                    ];

                    $goodsList[] = $arr;
                }
                if (empty($goodsList)) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '未查询到商品'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $goodsList,
                        'count' => $goodsModel->where($where)->count()
                    ];
                }

                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询商品列表失败：' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到商品信息');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'cateNames' => $cateNames
            ]);
        }
    }

    /**
     * 添加商品
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsModel $goodsModel
     * @return \think\response\Json|\think\response\View
     */
    public function addGoods(Request $request, GoodsCate $goodsCateModel, GoodsModel $goodsModel)
    {
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\Goods');
            if ($result !== true) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $result);
            }
            Db::startTrans();
            try {
                $goodsModel->addGoods();

                AdminLog::addLog('添加商品', $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('添加商品失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加失败');
            }
        } else {
            $cateNames = $goodsCateModel->getSortCateName();
            foreach ($cateNames as $k => $v) {
                $cateNames[$k]['name'] = str_repeat('--------', $v['level']) . $v['name'];
            }

            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'cateNames' => $cateNames
            ]);
        }
    }

    /**
     * 修改商品
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsModel $goodsModel
     * @return \think\response\Json|\think\response\View
     */
    public function editGoods(Request $request, GoodsCate $goodsCateModel, GoodsModel $goodsModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $goodsModel->getGoodsInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到商品信息');
        }
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\Goods');
            if ($result !== true) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $result);
            }
            Db::startTrans();
            try {
                $info->editGoods();

                AdminLog::addLog('修改商品', $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改商品失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
        } else {
            $cateNames = $goodsCateModel->getSortCateName();

//            dump(get_sub_image($info['picture'], 0, $info['goods_id'], '100', '100'));

            $info['goods_img'] = $info['goods_img'] != '' ? explode(',', $info['goods_img']) : [];

            $assignData = [
                'cateNames' => $cateNames,
                'info' => $info
            ];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
        }
    }

    /**
     * 删除商品
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsModel $goodsModel
     * @return \think\response\Json|\think\response\View
     */
    public function delGoods(Request $request, GoodsModel $goodsModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intval');
            $info = $goodsModel->getGoodsInfoById($id);
            if (empty($info)) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到商品信息');
            }
            Db::startTrans();
            try {

                $info->delGoods();

                AdminLog::addLog('删除商品', $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('删除商品失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }

    /**
     * 获取规格
     * @param Request $request
     * @param GoodsCate $goodsCateModel
     * @param GoodsSpec $goodsSpecModel
     * @param GoodsSpecItem $goodsSpecItemModel
     * @param GoodsSpecPrice $goodsSpecPriceModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function getSpec(Request $request, GoodsCate $goodsCateModel, GoodsSpec $goodsSpecModel, GoodsSpecItem $goodsSpecItemModel, GoodsSpecPrice $goodsSpecPriceModel)
    {
        $cateId = $request->param('cate_id', '', 'intval');
        $goodsId = $request->param('goods_id', 0, 'intval');
        $cateInfo = $goodsCateModel->getCateInfoById($cateId);
        if (empty($cateInfo)) {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
        }

        $goodsSpec = $goodsSpecModel->getSpecByIds(explode(',', $cateInfo['spec_key']));
        $goodsItem = $goodsSpecItemModel->getSpecItemBySpecIds(array_keys($goodsSpec));
        $goodsSpecItem = [];
        foreach ($goodsItem as $k2 => $v2) {
            $goodsSpecItem[$v2['spec_id']][] = $v2;
        }

        $goodsItemPrice = $goodsSpecPriceModel->getItemKeyByGoodsId($goodsId);
        foreach ($goodsItemPrice as $k => $v) {
            $goodsItemPrice[$k] = (int)$v;
        }
        $assignData = [
            'goodsSpec' => $goodsSpec,
            'goodsSpecItem' => $goodsSpecItem,
            'goodsItemPrice' => $goodsItemPrice
        ];
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
    }

    /**
     * 获取规格参数项
     * @param Request $request
     * @param GoodsSpecItem $goodsSpecItemModel
     * @param GoodsSpec $goodsSpecModel
     * @param GoodsSpecPrice $goodsSpecPriceModel
     * @return \think\Response|\think\response\Json
     */
    public function getSpecInput(Request $request, GoodsSpecItem $goodsSpecItemModel, GoodsSpec $goodsSpecModel, GoodsSpecPrice $goodsSpecPriceModel)
    {
//        dump($request->param());
        $specArr = $request->param('spec_arr');
        $goodsId = $request->param('goods_id', 0, 'intval');

        if (empty($specArr)) {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
        }


        $goodsItemIds = [];
        foreach ($specArr as $v) {
            foreach ($v as $val) {
                $goodsItemIds[] = $val;
            }
        }
        $goodsItems = $goodsSpecItemModel->whereIn('id', $goodsItemIds)->column('name,spec_id', 'id');
        $specIds = array_keys($specArr);
        $specs = $goodsSpecModel->whereIn('id', $specIds)->column('name', 'id');

        $goodsItemIds = [];
        $goodsItem2 = array_shift($specArr);
        foreach ($goodsItem2 as $v) {
            $goodsItemIds[] = array($v);
        }
        foreach ($specArr as $key => $item) {
            $goodsItemIds = combine_array($goodsItemIds, $item);
        }

        $goodsSpecPrice = $goodsSpecPriceModel->where('goods_id', (int)$goodsId)->column('*', 'item_key');

        $assignData = [
            'specs' => $specs,
            'goodsItems' => $goodsItems,
            'goodsItemIds' => $goodsItemIds,
            'specIds' => $specIds,
            'goodsSpecPrice' => $goodsSpecPrice
        ];
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
    }

    /**
     * 规格列表
     * @param Request $request
     * @param Where $where
     * @param GoodsSpec $goodsSpecModel
     * @return \think\response\Json|\think\response\View
     */
    public function specList(Request $request, Where $where, GoodsSpec $goodsSpecModel)
    {
        try {
            if ($name = $request->param('name', '', 'trim')) {
                $where['name'] = ['like', '%' . $name . '%'];
            }
            $page = $request->get('p', '1', 'int') - 1;
            $pageSize = $request->get('p_num', '10', 'int');
            $list = $goodsSpecModel->where($where)->limit($page * $pageSize, $pageSize)->order('id desc')->select();

            $specList = [];
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'name' => $v['name'],
                ];

                $specList[] = $arr;
            }
            if (empty($specList)) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '未获取到商品规格'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $specList,
                    'count' => $goodsSpecModel->where($where)->count()
                ];
            }

            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询规格列表失败：' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
    }

    /**
     * 添加规格
     * @param Request $request
     * @param GoodsSpec $goodsSpecModel
     * @return \think\response\Json|\think\response\View
     */
    public function addSpec(Request $request, GoodsSpec $goodsSpecModel)
    {
        $result = $this->validate($request->post(), 'app\admin\validate\GoodsSpec');
        if ($result !== true) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $result);
        }
        Db::startTrans();
        try {
            $goodsSpecModel->addSpec();

            AdminLog::addLog('添加商品规格', $request->param(), $this->adminUser['admin_id']);

            Db::commit();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('添加商品规格失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
        }
    }

    /**
     * 编辑规格
     * @param Request $request
     * @param GoodsSpec $goodsSpecModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editSpec(Request $request, GoodsSpec $goodsSpecModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $goodsSpecModel->getInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\GoodsSpec');
            if ($result !== true) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $result);
            }
            Db::startTrans();
            try {
                $info->editSpec();

                AdminLog::addLog('修改商品规格', $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改商品规格失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
        } else {
            $info['spec_value'] = implode(',', $goodsSpecModel->getSpecItemBySpecId($info['id']));
            $assignData = [
                'info' => $info
            ];
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $assignData);
        }
    }

    /**
     * 删除商品规格
     * @param Request $request
     * @param GoodsSpec $goodsSpecModel
     * @return \think\Response|\think\response\Json
     */
    public function delSpec(Request $request, GoodsSpec $goodsSpecModel)
    {
        Db::startTrans();
        try {
            $id = $request->param('id', '', 'intval');
            $info = $goodsSpecModel->getInfoById($id);
            if (empty($info)) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
            }
            $data = $info->toArray();
            $info->delete();
            $info->_afterDelete();

            AdminLog::addLog('删除商品规格', $data, $this->adminUser['admin_id']);

            Db::commit();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('删除商品规格失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
        }

    }


}
