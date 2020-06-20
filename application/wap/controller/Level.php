<?php

namespace app\wap\controller;

use think\Request;
use app\common\model\grade\Level as LevelModel;
use app\common\logic\LevelLogic;

class Level extends Base
{

    /**
     * 所有的会员级别
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAjaxLevelList(Request $request, LevelModel $levelModel)
    {
        if ($request->isAjax()) {
            $levelList = $levelModel->where('level_id', '<', '38')->select();
            return json(['code' => 1, 'levelList' => $levelList]);
        }
    }

    /**
     * 修改登录密码操作
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function levelUserAdd(Request $request)
    {
        if ($request->isPost()) {
            $LevelLogic = new LevelLogic();
            try {
                $LevelLogic->dolevelUserAdd();
                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

}
