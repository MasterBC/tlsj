<?php

namespace app\wap\controller;

use app\common\model\block\BlockLog;
use app\common\model\block\UsersBlock;
use think\Request;

class Block extends Base
{

    public function logList(Request $request)
    {
        $blockLogTypes = block_log_type();
        if ($request->isAjax()) {

            $list = BlockLog::getUserBlockLogList($this->user_id);
            $blockInfo = \app\common\model\block\Block::getBlockInfoField('id,logo');

            return view('block/log_list_ajax', [
                'list' => $list,
                'blockLogTypes' => $blockLogTypes,
                'blockInfo' => $blockInfo
            ]);
        } else {
            return view('block/log_list', [
                'blockLogTypes' => $blockLogTypes
            ]);
        }
    }

    /**
     * 获取货币
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAjaxUserOwnedBlockArr(Request $request, UsersBlock $usersBlockModel)
    {
        if ($request->isAjax()) {
            $userOwnedBlock = $usersBlockModel->getUserOwnedBlock($this->user_id);
            foreach($userOwnedBlock as $k => $v) {
                $userOwnedBlock[$k] = (float)$v;
            }
            return json(['code' => 1, 'userOwnedBlockAll' => $userOwnedBlock]);
        }
    }

}
