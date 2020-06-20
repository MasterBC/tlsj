<?php
namespace app\wap\controller;

use think\Request;
use app\common\model\Help as HelpModel;
use app\common\model\HelpCate;

class Help extends Base
{
    /**帮助中心
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function helpIndex(Request $request)
    {
        if ($request->isAjax()){
            $HelpModel = new HelpModel();

            $helpInfo = $HelpModel->getHelpIndex();
            $cateInfo = HelpCate::getHelpCateNames();
            $this->assign('cateInfo',$cateInfo);
            $this->assign('helpInfo',$helpInfo);
            return view('help/help_index_ajax');
        }
            return view('help/help_index');
    }

    /**帮助中心详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function helpDetails(Request $request)
    {
        $Id = $request->param('id');

        $helpInfo = HelpModel::getHelpInfoById($Id);
        $cateInfo = HelpCate::getHelpCateNames();
        $this->assign('cateInfo',$cateInfo);
        $this->assign('helpInfo',$helpInfo);
        return view('help/help_details');

    }

}

