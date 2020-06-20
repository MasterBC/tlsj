<?php

namespace app\wap\controller;

use app\common\model\Users;
use think\Request;
use app\common\model\block\BlockChangeLog;
use app\common\logic\BlockLogic;
use app\common\model\block\BlockChange as BlockChangeModel;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class BlockChange extends Base
{
    /**
     * BlockChange constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assign('blockNames', get_block_name());
    }

    /**
     * 转账日志
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function indexChange(Request $request)
    {
        if ($request->isAjax()) {
            $blockChangeLogModel = new BlockChangeLog();

            $blockChangeLogData = $blockChangeLogModel->getBlockChangeLog($this->user_id, $this->user_id);
            return view('change_block/change_index_ajax', ['blockChangeLogData' => $blockChangeLogData]);
        } else {
            $bid = $request->param('bid');
            return view('change_block/change_index', ['bid' => $bid]);
        }
    }

    /**
     * 货币转账详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeDetail(Request $request)
    {
        // 获取参数
        $id = $request->param('id', '', 'int');
        $changeBlockInfo = BlockChangeLog::where('id', $id)->find();
        $this->assign('changeBlockInfo', $changeBlockInfo);
        $this->assign('toUserAccount', Users::where('user_id', $changeBlockInfo['to_uid'])->value('account'));
        return view('change_block/change_detail');
    }

    /**
     * 货币转账列表
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeInfo()
    {
        // 查出所有的转账参数
        $changeBlockData = (new BlockChangeModel())->getChangeBlockData('', ['id', 'bid', 'low', 'bei', 'out', 'fee']);

        return view('change_block/change_info', ['changeBlockData' => $changeBlockData]);
    }

    /**
     * 转账申请
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeAdd(Request $request)
    {
        if ($request->isAjax()) {
            // 判断用户是否设置了二级密码
            $this->checkPayPasswordIsSet();

            // 获取转账参数数据
            $data = $request->post();
            $blockLogic = new BlockLogic();

            // 验证类
            $result = $this->validate($data, 'app\wap\validate\ChangeBlock');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {
                $blockLogic->doBlockChange($data, $this->user);
                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            // 获取传过来的货币转账参数id
            $id = intval($request->param('id'));
            $blockChangeInfo = (new BlockChangeModel())->getChangeBlockData($id, ['bid', 'to_bid'], 2);
            $usersBlock = get_Block_amount($this->user_id, $blockChangeInfo['bid'], 1);
            $this->assign('usersBlock', $usersBlock);
            $this->assign('blockChangeInfo', $blockChangeInfo);
            return view('change_block/change_add');
        }
    }


    /**
     * 转入日志
     * @param Request $request
     * @return type
     */
    public function toChange(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('change_block/to_change');
        }
    }

    /**
     * 转出日志
     * @param Request $request
     * @return type
     */
    public function changeOut(Request $request)
    {
        if ($request->isAjax()) {

        } else {
            return view('change_block/change_out');
        }
    }

}
