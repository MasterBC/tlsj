<?php

namespace app\wap\controller;

use think\Request;
use app\common\logic\BlockLogic;
use app\common\model\block\BlockTransformLog;
use app\common\model\block\BlockTransform as BlockTransformModel;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class BlockTransform extends Base
{
    /**
     * BlockTransform constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assign('blockNames', get_block_name());
    }

    /**
     * 转换日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexTransform(Request $request)
    {
        if ($request->isAjax()) {
            $blockTransformLogModel = new BlockTransformLog();

            $blockTransformLogInfo = $blockTransformLogModel->getBlockTransformLog($this->user_id);
            $this->assign('blockTransformLogInfo', $blockTransformLogInfo);
            return view('transform_block/transform_index_ajax');
        } else {
            $this->assign('bid', $request->param('bid', '', 'int'));
            return view('transform_block/transform_index');
        }
    }

    /**
     * 转换详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformDetail(Request $request)
    {
        // 获取参数
        $id = $request->param('id', '', 'int');
        $transformBlockInfo = BlockTransformLog::where('id', $id)->find();
        $this->assign('transformBlockInfo', $transformBlockInfo);
        return view('transform_block/transform_detail');
    }

    /**
     * 转账申请
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformAdd(Request $request)
    {
        if ($request->isAjax()) {
            // 判断用户是否设置了二级密码
            $this->checkPayPasswordIsSet();

            // 获取转账参数数据
            $data = $request->post();
            $blockLogic = new BlockLogic();

            // 验证类
            $result = $this->validate($data, 'app\wap\validate\TransformBlock');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {
                $blockLogic->doTransformAdd($this->user);
                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            // 获取传过来的id
            $id = $request->param('id', '', 'int');
            $this->assign('transformInfo', BlockTransformModel::where('id', $id)->field('bid,to_bid')->find());
            $this->assign('userBlock', get_block_amount($this->user_id, $id, 1));
            return view('transform_block/transform_add');
        }
    }

    /**
     * 转换列表
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformInfo()
    {
        $this->assign('transformInfo', BlockTransformModel::where('status', 1)->field('id,bid,to_bid,bei,out,fee,per,low')->select());
        return view('transform_block/transform_info');
    }

}
