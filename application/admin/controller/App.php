<?php

namespace app\admin\controller;

use app\common\model\AdminLog;
use app\common\model\app\AppVersion;
use think\db\Where;
use think\facade\Log;
use think\facade\Validate;
use think\Request;

class App extends Base
{

    /**
     * 版本列表
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function versionList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            try {
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = AppVersion::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $versionList = [];
                foreach ($list as $v) {
                    $arr = $v;
                    $arr['update_time'] = date('Y-m-d H:i:s', $arr['update_time']);

                    $versionList[] = $arr;
                }

                $data = [
                    'code' => 1,
                    'data' => $versionList,
                    'count' => AppVersion::where($where)->count()
                ];

                return json()->data($data);
            } catch (\Exception $e) {
                $data = [
                    'code' => -1,
                    'msg' => '未获取到版本列表'
                ];
                Log::write('查询app版本列表失败：' . $e->getMessage(), 'error');
                return json()->data($data);
            }
        } else {
            return view('app/version_list');
        }
    }

    /**
     * 发布版本
     * @param Request $request
     * @param AppVersion $appVersionModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addVersion(Request $request, AppVersion $appVersionModel)
    {
        if ($request->isPost()) {
            $validate = Validate::make([
                'system' => 'require',
                'version' => 'require',
                'update_url' => 'require',
                'update_content' => 'require',
            ], [
                'system.require' => '请选择系统',
                'version.require' => '请输入版本号',
                'update_url.require' => '请输入更新地址',
                'update_content.require' => '请输入更新内容'
            ]);
            $data = $request->param();
            if (!$validate->check($data)) {
                return json()->data(['code' => -1, 'msg' => $validate->getError()]);
            }
            try {
                $data['update_time'] = time();
                $appVersionModel->allowField(true)->save($data);

                AdminLog::addLog('发布app版本', $request->param());
                return json()->data(['code' => 1, 'msg' => '发布成功']);
            } catch (\Exception $e) {

                Log::write('发布版本失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '发布失败']);
            }
        } else {
            return view('app/add_version');
        }
    }

    /**
     * 修改版本信息
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editVersion(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = AppVersion::getVersionInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }

        if ($request->isPost()) {
            $validate = Validate::make([
                'system' => 'require',
                'version' => 'require',
                'update_url' => 'require',
                'update_content' => 'require',
            ], [
                'system.require' => '请选择系统',
                'version.require' => '请输入版本号',
                'update_url.require' => '请输入更新地址',
                'update_content.require' => '请输入更新内容'
            ]);
            $data = $request->param();
            if (!$validate->check($data)) {
                return json()->data(['code' => -1, 'msg' => $validate->getError()]);
            }
            try {
                $data['update_time'] = time();
                $info->allowField(true)->save($data);
                $info->_afterUpdate();

                AdminLog::addLog('修改app版本信息', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {

                Log::write('修改版本信息失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '修改失败']);
            }
        } else {
            return view('app/edit_version', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除版本信息
     * @param Request $request
     * @return \think\Response|\think\response\Json
     */
    public function delVersion(Request $request)
    {
        try {
            $id = $request->param('id', '', 'intval');
            $info = AppVersion::getVersionInfoById($id);
            if (empty($info)) {
                return json()->data(['code' => -1, 'msg' => '未获取版本信息']);
            }

            $oldInfo = $info->toArray();
            $info->delete();
            AdminLog::addLog('删除app版本信息', $oldInfo);
            return json()->data(['code' => 1, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            Log::write('删除app版本失败: ' . $e->getMessage(), 'error');
            return json()->data(['code' => -1, 'msg' => '删除失败']);
        }
    }
}
