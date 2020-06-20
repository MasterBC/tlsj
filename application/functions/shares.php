<?php

/**
 * 获取股票名称
 * @param int $id
 * @return string
 */
function get_shares_name($id = 0)
{
    $data = \app\common\model\shares\Shares::getSharesNames();
    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}

/**
 * 获取股票变动类型
 * @param int $type
 * @return mixed
 */
function shares_log_type($type = 0)
{
    $data = get_shares_name();
    $data[1] = "赠送";
    $data[2] = "买入";
    $data[3] = "卖出";
    $data[4] = "拆送";
    $data[5] = "冻结";
    $data[6] = "释放";
    $data[7] = "管理员操作";
//    $data[8] = "变更资料";
    $data[10] = "挂买";
    $data[11] = "挂卖";
    $data[9] = "卖出撤回";
    $data[12] = "买入撤回";
    $data[13] = "众筹";

    $data[0] = $data;

    return isset($data[intval($type)]) ? $data[intval($type)] : '';
}

/**
 * 获取用户的股票金额
 * @param $userId
 * @param $moneyId
 * @param int $type
 * @return float
 */
function get_shares_amount($uid, $sid, $type = 1)
{
    return \app\common\model\shares\SharesUser::getSharesUserByUserId($uid, $sid, $type);
}