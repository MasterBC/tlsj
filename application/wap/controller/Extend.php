<?php

namespace app\wap\controller;

use app\common\model\game\Game as GameModel;
use app\common\model\game\GameUser;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Extend extends Base
{

    /**
     * 应用中心首页
     * @return type
     */
    public function index(GameModel $gameModel)
    {

        $gameOneList = $gameModel->where('status', 1)->where('is_type', 1)->select();
        $gameTwoList = $gameModel->where('status', 1)->where('is_type', 2)->select();
        return view('extend/index', [
            'gameOneList' => $gameOneList,
            'gameTwoList' => $gameTwoList
        ]);
    }

}
