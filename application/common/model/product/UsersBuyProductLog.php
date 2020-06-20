<?php

namespace app\common\model\product;

use app\common\model\Common;

class UsersBuyProductLog extends Common
{
    protected $name = 'users_buy_product_log';


    /**
     * 获取会员购买某个产品数量
     *
     * @param  int $userId 会员id
     * @param  int $productId 产品id
     * @return int
     * @author gkdos
     * 2019-09-21T20:41:01+0800
     */
    public static function getUserBuyProductNum($userId, $productId)
    {
    	return self::where('user_id', $userId)
    		->where('product_id', $productId)
    		->count();
    }

    /**
     * 添加会员购买记录
     *
     * @param  int $userId 会员id
     * @param  int $productId 产品id
     * @param  float $amount 消耗金额
     * @author gkdos
     * 2019-09-21T20:34:47+0800
     */
    public static function addBuyLog($userId, $productId, $amount)
    {
    	$data = [
    		'user_id' => $userId,
    		'product_id' => $productId,
    		'amount' => $amount,
    		'add_time' => time()
    	];

    	return self::insertGetId($data);
    }
}