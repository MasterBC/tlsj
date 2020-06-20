<?php

/**
 * 金额转换
 *
 * @param $money
 * @return string
 * @author No_door
 * 2019-09-29 21:24:47
 */
function moneyTransformation($money)
{
    if ($money >= 1000000000000000000) {
        $num = number_format($money / 1000000000000000000, 2, '.', '') . ' t';
        $num = floatval($num) . ' 1aa';
    } elseif ($money >= 1000000000000000) {
        $num = number_format($money / 1000000000000000, 2, '.', '') . ' t';
        $num = floatval($num) . ' aa';
    } elseif ($money >= 1000000000000) {
        $num = number_format($money / 1000000000000, 2, '.', '') . ' t';
        $num = floatval($num) . ' t';
    } elseif ($money >= 1000000000) {
        $num = number_format($money / 1000000000, 2, '.', '') . ' b';
        $num = floatval($num) . ' b';
    } elseif ($money >= 1000000) {
        $num = number_format($money / 1000000, 2, '.', '') . ' m';
        $num = floatval($num) . ' m';
    } elseif ($money >= 1000) {
        $num = number_format($money / 1000, 2, '.', '') . ' k';
        $num = floatval($num) . ' k';
    } else {
        $num = $money;
        $num = number_format($num, 2, '.', '');
        $num = floatval($num);
    }

    return $num;
}

/**
 * 获取钱包名称
 * @param int $id
 * @return string
 */
function get_money_name($id = 0)
{
    $data = \app\common\model\money\Money::getMoneyNames();

    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}

/**
 * 获取钱包金额
 * @param $userId
 * @param $moneyId
 * @param int $type
 * [
 *      1 => '会员可用金额',
 *      2 => '会员冻结金额',
 *      3 => '总金额'
 * ]
 * @return mixed
 */
function get_money_amount($userId, $moneyId, $type = 1)
{
    return \app\common\model\money\UsersMoney::getUsersMoneyByUserId($userId, $moneyId, $type);
}

/**
 * 获取钱包信息
 * @param $moneyId
 * @return mixed
 */
function get_money_info($moneyId)
{
    return \app\common\model\money\Money::getMoneyInfoById($moneyId);
}

/**
 * 获取钱包变动类型
 * @param int $type
 * @return mixed
 */
function money_log_type($type = 0)
{
    $data = get_bonus_name();
    $data[100] = '注册赠送';
    //    $data[102] = '转入';
    $data[103] = '管理调整';
    //    $data[104] = '管理操作冻结';
    //    $data[105] = '释放';
    //    $data[106] = '购买众筹';
    //    $data[107] = '汇款充值';
    $data[108] = '转盘抽奖';
    //    $data[109] = '欢乐夹娃娃';
    $data[120] = '申请提现';
    $data[121] = '拒绝提现';
    $data[150] = '实名认证';
    $data[151] = '邀请好友';
    //    $data[151] = '挂买';
    //    $data[152] = '互助出售';
    //    $data[153] = '股票买入';
    //    $data[154] = '股票出售';
    //    $data[155] = '股票挂买撤销';
    //    $data[156] = '股票众筹';
    $data[160] = '签到';
    $data[171] = '购买';
    $data[172] = '删除牛';
    $data[173] = '红包';
    $data[174] = '在线收益';
    $data[175] = '离线收益';
    $data[176] = '奖励';
    $data[177] = '互动广告';
    $data[178] = '分红收入';

    $data[0] = $data;

    return isset($data[intval($type)]) ? $data[intval($type)] : '';
}

/**
 * 获取钱包冻结类型
 * @param int $type
 * @return mixed
 */
function money_lock_log_type($type = 0)
{
    $data = get_bonus_name();
    $data[101] = '管理操作';
    $data[0] = $data;

    return isset($data[intval($type)]) ? $data[intval($type)] : '';
}


/**
 * 获取会员最大的  人物等级
 */
function get_max_product_info($userId)
{
    return \app\common\model\product\UsersProduct::where('user_id', $userId)->max('product_id');
}