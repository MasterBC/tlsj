<?php

namespace app\wap\controller;
use app\common\model\NewsCate;
use think\Controller;
use think\Request;
use think\db;
use app\common\model\News as NewsModel;

/**
 * Class News
 * @package app\wap\controller
 */
class News extends Base
{
    /**
     * 新闻中心
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function logNews(Request $request)
    {
        $NewsCateModel = new NewsCate();
        $newsCateData = $NewsCateModel->getNewsCateField('cate_id,title');

        if ($request->isAjax()){
            $NewsModel = new NewsModel();
            $newsInfo = $NewsModel->getNewsLog();
            return view('news/news_list_ajax',['newsInfo'=>$newsInfo]);
        }else{
            return view('news/news_list',['newsCateData'=>$newsCateData]);
        }
    }

    /**
     * 新闻详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function newsCont(Request $request)
    {
        $id = $request->param('id');

        $NewsModel = new NewsModel();

        $newsInfo = $NewsModel->getNewsById($id);
        $lastData = $newsInfo->getPrevNewsInfo();
        $nextData = $newsInfo->getNextNewsInfo();

        return view('news/news_cont',['lastData'=>$lastData,'nextData'=>$nextData,'Info'=>$newsInfo]);
    }
}
