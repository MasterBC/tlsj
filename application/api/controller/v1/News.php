<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\response\ReturnCode;
use app\common\model\News as NewsModel;
use app\common\model\NewsCate;
use think\db\Where;
use think\Request;

class News extends Base
{

    /**
     * 获取新闻分类
     * @return \think\Response|\think\response\Json
     */
    public function getCategoryList()
    {
        $cateNames = NewsCate::getNewsCateNames();
        $data = [];
        foreach ($cateNames as $k => $v) {
            $data[] = [
                'id' => $k,
                'name' => $v
            ];
        }
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
    }

    /**
     * 获取新闻列表
     * @param Request $request
     * @param Where $where
     * @param NewsModel $newsModel
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList(Request $request, Where $where, NewsModel $newsModel)
    {
        $where['status'] = 1;
        $list = [];
        $page = $request->param('page', 1, 'intval');
        $page = $page - 1;
        $pageSize = $request->param('rows', 10, 'intval');
        if ($cateId = $request->param('cate_id', '', 'intval')) {
            $where['cate_id'] = $cateId;
            $list[$cateId] = $newsModel->where($where)->limit($page * $pageSize, $pageSize)->select();
        } else {
            $cateNames = NewsCate::getNewsCateNames();
            foreach ($cateNames as $k => $v) {
                $where['cate_id'] = $k;
                $list[$k] = $newsModel->where($where)->limit($page * $pageSize, $pageSize)->select();
            }
        }

        $data = [];
        foreach ($list as $key => $val) {
            $data[$key] = [];
            foreach ($val as $v) {
                $data[$key][] = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'thumb' => get_img_domain() . $v['thumb'],
                    'desc' => $v['desc'],
                    'content' => htmlspecialchars_decode($v['content'])
                ];
            }
        }

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
    }

    /**
     * 获取新闻详情
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail(Request $request)
    {
        $id = (int)$request->param('id');
        if ($id <= 0) {
            return ReturnCode::showReturnCode(1005);
        }
        $info = NewsModel::getNewsById($id);
        if (empty($info) || $info['status'] != 1) {
            return ReturnCode::showReturnCode(1201);
        }

        $data = [
            'id' => $info['id'],
            'title' => $info['title'],
            'thumb' => get_img_domain() . $info['thumb'],
            'desc' => $info['desc'],
            'content' => htmlspecialchars_decode($info['content'])
        ];

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
    }
}