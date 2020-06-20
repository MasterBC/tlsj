<?php

namespace app\common\model\product;

use app\common\model\Common;

class UsersProduct extends Common
{
    protected $name = 'users_product';

    public static $addType = [
        1 => '购买',
        2 => '合成'
    ];

    // 状态
    public static $status = [
        1 => '正常',
        2 => '禁用',
        3 => '已合成',
        4 => '已删除'
    ];

    /**
     * 根据会员id获取会员持有的产品数
     *
     * @param  int $userId 会员id
     * @return int
     * @author gkdos
     * 2019-09-20T17:45:01+0800
     */
    public static function getUserProductNumByUserId($userId)
    {
    	return self::where('user_id', $userId)->where('status', 1)->count();
    }

    /**
     * 添加会员产品信息
     *
     * @param  int $userId 会员id
     * @param  int $productId 产品id
     * @param  float $amount 花费金额
     * @param  float $income 收益
     * @param  int $position 位置
     * @param  int $addType 添加类型
     * @author gkdos
     * 2019-09-20T17:53:30+0800
     */
    public static function addUserProduct($userId, $productId, $amount, $income, $position = 0, $addType = 1)
    {
        if($position > 12 || $position < 1) {
            $position =  self::getPosition($userId);
        }
    	$data = [
    		'user_id' => $userId,
    		'product_id' => $productId,
    		'add_time' => time(),
    		'amount' => $amount,
    		'income' => $income,
    		'position' => $position,
            'add_type' => $addType
    	];

    	return self::insertGetId($data);
    }

    /**
     * 获取位置
     *
     * @param  int $userId 会员id
     * @return [type]
     * @author gkdos
     * 2019-09-20T21:06:36+0800
     */
    public static function getPosition($userId)
    {
    	$userProductList = self::where('user_id', $userId)
            ->where('status', 1)
	    	->order('position', 'asc')
	    	->column('id', 'position');

	    for($i = 1; $i <= 12; $i++) {
	    	if(!isset($userProductList[$i])) {
	    		return $i;
	    	}
	    }

		return 1;
    }

    /**
     * 根据id查询会员产品信息
     *
     * @param  id $id 会员产品id
     * @return array
     * @author gkdos
     * 2019-09-21T11:51:46+0800
     */
    public static function getUserProductInfoById($id)
    {
        return self::where('id', $id)->find();
    }

    /**
     * 根据会员产品id和会员id查询会员产品信息
     *
     * @param  int $id 会员产品id
     * @param  int $userId 会员id
     * @return array
     * @author gkdos
     * 2019-09-23T16:14:10+0800
     */
    public static function getUserProductInfoByIdAndUserId($id, $userId)
    {
        return self::where('id', $id)->where('user_id', $userId)->find();
    }

    /**
     * 获取会员最大产品id
     *
     * @param  int $userId 会员id
     * @return array
     * @author gkdos
     * 2019-09-23T21:09:20+0800
     */
    public static function getUserMaxProductIdByUserId($userId)
    {
        $maxProductId = self::where('user_id', $userId)->max('product_id');
        if($maxProductId > 0) {
            return $maxProductId;
        } else {
            return 1;
        }
    }

}