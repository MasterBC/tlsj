<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use think\Request;
use app\common\model\News as NewsModel;
use app\common\model\NewsCate as NewsCateModel;
use app\common\model\Help as HelpModel;
use app\common\model\HelpCate as HelpCateModel;
use app\common\model\Notice as NoticeModel;
use app\common\model\About as AboutModel;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use think\db;

class Article extends Base
{
    /**
     * 新闻列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function newsList(Request $request, Where $where)
    {
        $newsCate = NewsCateModel::getNewsCateNames();
        if ($request->param('is_get_data') == true) {
            try {
                // 获取搜索参数
                $cateId = $request->param('cate_id', '', 'int');
                $cateId && $where['cate_id'] = $cateId;
                $title = $request->param('title', '', 'trim');
                $title && $where['title'] = ['like', '%' . $title . '%'];

                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = NewsModel::where($where)->limit($page * $pageSize, $pageSize)->order('id desc')->select();

                $configList = [];
                foreach ($list as $v) {
                    $arr = $v;
                    $arr['cate_id'] = $newsCate[$v['cate_id']] ?? '';
                    $arr['thumb'] = get_img_domain() . $v['thumb'];
                    $arr['content'] = htmlspecialchars_decode($v['content']);
                    $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);

                    $configList[] = $arr;
                }

                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $configList,
                    'count' => NewsModel::where($where)->count()
                ];

                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询新闻信息失败：' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'newsCate' => $newsCate
            ]);
        }
    }

    /**
     * 添加新闻列表信息
     * @param Request $request
     * @param NewsModel $newsModel
     * @return \think\response\Json|\think\response\View
     */
    public function addNews(Request $request, NewsModel $newsModel)
    {
        if ($request->isPost()) {
            try {
                $data = $request->only([
                    'cate_id', 'content', 'desc', 'title', 'thumb', 'keywords', 'status'
                ]);
                $data['add_time'] = time();
                // 写入数据
                $newsModel->insertGetId($data);
                AdminLog::addLog('添加新闻信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('添加新闻信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
        } else {
            $newsCateNames = NewsCateModel::getNewsCateNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'newsCateNames' => $newsCateNames
            ]);
        }
    }

    /**
     * 编辑新闻列表信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editNews(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = NewsModel::getNewsById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $data = $request->param();
                $data["content"] = htmlspecialchars($request->param('content'), ENT_NOQUOTES);
                $info->allowField([
                    'cate_id', 'title', 'thumb', 'keywords', 'desc', 'content', 'add_time', 'status'
                ])->save($data);

                $info->_afterUpdate();

                AdminLog::addLog('修改新闻信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改新闻信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            $newsCateNames = NewsCateModel::getNewsCateNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'newsCateNames' => $newsCateNames
            ]);
        }
    }

    /**
     * 删除新闻数据
     * @param Request $request
     * @return \think\response\Json
     */
    public function delNews(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intval');
            try {
                $info = NewsModel::getNewsById($id);

                if (empty($info)) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
                }
                $deleteInfo = $info->toArray();

                $res = $info->delete();
                if ($res) {
                    AdminLog::addLog('删除新闻信息', $deleteInfo, $this->adminUser['admin_id']);
                    $info->_afterDelete();
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
                }
            } catch (\Exception $e) {
                Log::write('删除新闻失败：' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }

    /**
     * 新闻分类
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function newsCateList(Where $where)
    {
        try {
            $list = NewsCateModel::where($where)->select();

            $configList = [];
            foreach ($list as $v) {
                $arr = $v;

                $configList[] = $arr;
            }

            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList
            ];

            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询新闻分类信息失败：' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到新闻分类信息');
        }
    }

    /**
     * 添加新闻列表信息
     * @param Request $request
     * @param NewsCateModel $newsCateModel
     * @return \think\response\Json|\think\response\View
     */
    public function addNewsCate(Request $request, NewsCateModel $newsCateModel)
    {
        try {
            // 获取参数
            $data["title"] = $request->param('title');
            $data["keywords"] = $request->param('keywords');
            $data["status"] = $request->param('status');
            $data["desc"] = $request->param('desc');
            // 写入数据
            $newsCateModel->insert($data);

            $newsCateModel->_afterInsert();
            AdminLog::addLog('添加新闻分类信息', $request->param(), $this->adminUser['admin_id']);
        } catch (\Exception $e) {
            Log::write('添加新闻分类信息失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $e->getMessage());
        }
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
    }

    /**
     * 编辑新闻分类信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editNewsCate(Request $request)
    {
        $id = $request->param('cate_id', '', 'intval');
        $info = NewsCateModel::getNewsCateById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $info->allowField([
                    'title', 'keywords', 'desc', 'status'
                ])->save($request->param());

                $info->_afterUpdate();

                AdminLog::addLog('修改新闻分类信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改新闻列表分类失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除新闻分类数据
     * @param Request $request
     * @return \think\response\Json
     * @throws \Exception
     */
    public function delNewsCate(Request $request)
    {
        if ($request->isPost()) {
            try {
                $id = $request->param('cate_id', '', 'intval');
                $info = NewsCateModel::getNewsCateById($id);

                if ($id <= 0) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '网络错误，请刷新后重试');
                }
                $deleteInfo = $info->toArray();

                $res = $info->delete();
                if ($res) {
                    AdminLog::addLog('删除新闻分类信息', $deleteInfo, $this->adminUser['admin_id']);
                    $info->_afterDelete();
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
                }
            } catch (\Exception $e) {
                Log::write('删除新闻分类失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }


    /**
     * 帮助列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function helpList(Request $request, Where $where)
    {
        $helpCate = HelpCateModel::getHelpCateNames();
        if ($request->param('is_get_data') == true) {
            try {
                // 获取搜索参数
                $cateId = $request->param('cate_id', '', 'int');
                $cateId && $where['cate_id'] = $cateId;
                $title = $request->param('title', '', 'trim');
                $title && $where['title'] = ['like', '%' . $title . '%'];

                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = HelpModel::where($where)->order('id desc')->limit($page * $pageSize, $pageSize)->select();

                $configList = [];
                foreach ($list as $v) {
                    $arr = $v;
                    $arr['cate_id'] = $helpCate[$v['cate_id']] ?? '';
                    $arr['thumb'] = get_img_domain() . $v['thumb'];
                    $arr['content'] = htmlspecialchars_decode($v['content']);
                    $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);

                    $configList[] = $arr;
                }

                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $configList,
                    'count' => HelpModel::where($where)->count()
                ];

                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询帮助信息失败：' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到帮助信息');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'helpCate' => $helpCate
            ]);
        }
    }

    /**
     * 添加帮助列表信息
     * @param Request $request
     * @param HelpModel $helpModel
     * @return \think\response\Json|\think\response\View
     */
    public function addHelp(Request $request, HelpModel $helpModel)
    {
        if ($request->isPost()) {
            try {
                // 获取参数
                $data["cate_id"] = $request->param('cate_id', '', 'int');
                $data["title"] = $request->param('title');
                $data["thumb"] = $request->param('thumb');
                $data["keywords"] = $request->param('keywords');
                $data["status"] = $request->param('status');
                $data["desc"] = $request->param('desc');
                $data["content"] = $request->param('content', '', 'htmlspecialchars');
                $data["add_time"] = time();
                // 写入数据
                $helpModel->insertGetId($data);
                AdminLog::addLog('添加帮助列表信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('添加帮助列表信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
        } else {
            $helpCate = HelpCateModel::getHelpCateNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'helpCate' => $helpCate
            ]);
        }
    }

    /**
     * 编辑帮助列表信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editHelp(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = HelpModel::getHelpInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $data = $request->param();
                $data['content'] = $request->param('content', '', 'htmlspecialchars');
                $info->allowField([
                    'cate_id', 'title', 'thumb', 'keywords', 'desc', 'content', 'add_time', 'status'
                ])->save($data);

                $info->_afterUpdate();

                AdminLog::addLog('修改帮助列表信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改帮助列表信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            $helpCate = HelpCateModel::getHelpCateNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'helpCate' => $helpCate
            ]);
        }
    }


    /**
     * 删除帮助数据
     * @param Request $request
     * @return \think\response\Json
     * @throws \Exception
     */
    public function delHelp(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intval');
            $info = HelpModel::getHelpInfoById($id);

            if ($id <= 0) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '网络错误，请刷新后重试');
            }
            $deleteInfo = $info->toArray();

            $res = $info->delete();
            if ($res) {
                $info->_afterDelete();
                AdminLog::addLog('删除帮助列表信息', $deleteInfo, $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }

    /**
     * 帮助分类
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function helpCateList(Where $where)
    {
        try {
            $list = HelpCateModel::where($where)->select();

            $configList = [];
            foreach ($list as $v) {
                $arr = $v;

                $configList[] = $arr;
            }

            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList
            ];

            return json()->data($data);
        } catch (\Exception $e) {

            Log::write('查询帮助分类信息失败：' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到帮助分类信息');
        }
    }

    /**
     * 编辑帮助列表信息
     * @param Request $request
     * @param HelpCateModel $helpCateModel
     * @return \think\response\Json|\think\response\View
     */
    public function addHelpCate(Request $request, HelpCateModel $helpCateModel)
    {
        try {
            // 获取参数
            $data["title"] = $request->param('title');
            $data["keywords"] = $request->param('keywords');
            $data["status"] = $request->param('status');
            $data["desc"] = $request->param('desc');
            // 写入数据
            $helpCateModel->insert($data);
            $helpCateModel->_afterInsert();
            AdminLog::addLog('添加帮助分类信息', $request->param(), $this->adminUser['admin_id']);
        } catch (\Exception $e) {
            Log::write('添加帮助分类信息失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
        }
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
    }

    /**
     * 编辑帮助分类信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editHelpCate(Request $request)
    {
        $id = $request->param('cate_id', '', 'intval');
        $info = HelpCateModel::getHelpCateById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $info->allowField([
                    'title', 'keywords', 'desc', 'status'
                ])->save($request->param());
                $info->_afterUpdate();

                AdminLog::addLog('修改帮助分类信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改帮助列表分类失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除帮助数据
     * @param Request $request
     * @return \think\response\Json
     * @throws \Exception
     */
    public function delHelpCate(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('cate_id', '', 'intval');
            $info = HelpCateModel::getHelpCateById($id);

            if ($id <= 0) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '网络错误，请刷新后重试');
            }
            $deleteInfo = $info->toArray();

            $res = $info->delete();
            if ($res) {
                $info->_afterUpdate();
                AdminLog::addLog('删除帮助分类', $deleteInfo, $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }


    /**
     * 公告列表
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function noticeList(Where $where)
    {
        try {
            $list = NoticeModel::where($where)->select();
            $configList = [];
            foreach ($list as $v) {
                $top = $v['top'] == 1 ? '置顶公告' : '未置顶公告';
                $arr = $v;
                $arr['thumb'] = get_img_domain() . $v['thumb'];
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['edit_time'] = date('Y-m-d H:i:s', $v['edit_time']);
                $arr['content'] = htmlspecialchars_decode($v['content']);
                $arr['top'] = $top;

                $configList[] = $arr;
            }

            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList
            ];

            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询公告信息失败：' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到帮助信息');
        }
    }

    /**
     * 添加公告信息
     * @param Request $request
     * @param NoticeModel $noticeModel
     * @return \think\response\Json|\think\response\View
     */
    public function addNotice(Request $request, NoticeModel $noticeModel)
    {
        try {
            // 获取参数
            $data["title"] = $request->param('title');
            $data["thumb"] = $request->param('thumb');
            $data["content"] = $request->param('content', '', 'htmlspecialchars');
            $data["add_time"] = time();
            $data["edit_time"] = time();
            $data["status"] = $request->param('status');
            $data["top"] = $request->param('top');
            // 写入数据
            $noticeModel->insertGetId($data);
            AdminLog::addLog('添加公告信息', $request->param(), $this->adminUser['admin_id']);
        } catch (\Exception $e) {
            Log::write('添加公告信息失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
        }
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
    }

    /**
     * 编辑公告信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editNotice(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = NoticeModel::getNoticeById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $data = $request->param();
                $data['edit_time'] = time();
                $data['content'] = $request->param('content', '', 'htmlspecialchars');
                $info->allowField([
                    'title', 'thumb', 'content', 'status', 'top', 'edit_time'
                ])->save($data);

                $info->_afterUpdate();

                AdminLog::addLog('修改公告信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改公告信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除公告数据
     * @param Request $request
     * @return \think\response\Json
     * @throws \Exception
     */
    public function delNotice(Request $request)
    {
        if ($request->isPost()) {
            $id = (int)$request->param('id');
            $info = NoticeModel::getNoticeById($id);

            if ($id <= 0) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '网络错误，请刷新后重试');
            }
            $deleteInfo = $info->toArray();

            $res = $info->delete();
            if ($res) {
                $info->_afterDelete();

                AdminLog::addLog('删除公告信息', $deleteInfo, $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }
    }


    /**
     * 单页列表
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function aboutList(Where $where)
    {
        try {
            $list = AboutModel::where($where)->select();
            $configList = [];
            $aboutType = AboutModel::$aboutType;
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'type' => $aboutType[$v['type']] ?? '',
                    'content_cn' => htmlspecialchars_decode($v['content_cn']),
                    'content_en' => htmlspecialchars_decode($v['content_en']),
                    'status' => $v['status'],
                ];

                $configList[] = $arr;
            }

            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList
            ];

            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询单页信息失败：' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到单页信息');
        }
    }

    /**
     * 编辑公告信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editAbout(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = AboutModel::getAboutById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $data = $request->param();
                $data['content_cn'] = $request->param('content_cn', '', 'htmlspecialchars');
                $data['content_en'] = $request->param('content_en', '', 'htmlspecialchars');
                $info->allowField([
                    'type', 'content_cn', 'content_en'
                ])->save($data);

                $info->_afterUpdate();

                AdminLog::addLog('修改单页信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改单页信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');

        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'aboutType' => AboutModel::$aboutType
            ]);
        }
    }
}
