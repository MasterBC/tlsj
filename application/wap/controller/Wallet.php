<?php

namespace app\wap\controller;

use think\Request;
use think\Db;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Wallet extends Base
{

    /**
     * 钱包首页
     * @param Request $request
     * @return type
     */
    public function walletIndex(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('wallet/wallet_index');
        }
    }

    /**
     * 创建钱包
     * @param Request $request
     * @return type
     */
    public function addWallet(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('wallet/add_wallet');
        }
    }

    /**
     * 创建成功
     * @param Request $request
     * @return type
     */
    public function addSuccess(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('wallet/add_success');
        }
    }

    /**
     * 导入钱包
     * @param Request $request
     * @return type
     */
    public function importWallet(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('wallet/import_wallet');
        }
    }

    /**
     * 什么是私钥？
     * @param Request $request
     * @return type
     */
    public function askProblem(Request $request)
    {
        if ($request->isAjax()) {
            
        } else {
            return view('wallet/ask_problem');
        }
    }

}
