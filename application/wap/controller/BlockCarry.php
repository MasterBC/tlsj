<?php

namespace app\wap\controller;

use think\Request;
use think\Db;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class BlockCarry extends Base
{

    /**
     * 提现列表
     * @param Request $request
     * @return type
     */
    public function indexCarry(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('carry_block/carry_index');
        }
    }

    /**
     * 提现详情
     * @param Request $request
     * @return type
     */
    public function carryDetail(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('carry_block/carry_detail');
        }
    }

    /**
     * 提现申请
     * @param Request $request
     * @return type
     */
    public function carryAdd(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('carry_block/carry_add');
        }
    }

    /**
     * 如果有多个币就显示
     * @param Request $request
     * @return type
     */
    public function carryInfo(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('carry_block/carry_info');
        }
    }

}
