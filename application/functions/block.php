<?php

/**
 * 获取货币名称
 * @param int $id
 * @return string|array
 */
function get_block_name($id = 0)
{
    $data = \app\common\model\block\Block::getBlockNames();

    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}

/**
 * 获取货币金额
 * @param $userId
 * @param $blockId
 * @param int $type
 * [
 *      1 => '会员可用金额',
 *      2 => '会员冻结金额',
 *      3 => '总金额'
 * ]
 * @return mixed
 */
function get_block_amount($userId, $blockId, $type = 1)
{
    return \app\common\model\block\UsersBlock::getAmountByUid($userId, $blockId, $type);
}

/**
 * 获取货币信息
 * @param $blockId
 * @return mixed
 */
function get_block_info($blockId)
{
    return \app\common\model\block\Block::getBlockInfoById($blockId);
}

/**
 * 获取货币变动类型
 * @param int $type
 * @return mixed
 */
function block_log_type($type = 0)
{
    $data = get_bonus_name();
//    $data[101] = '转出';
//    $data[102] = '转入';
//    $data[103] = '转换';
//    $data[104] = '管理调整';
//    $data[105] = '管理操作冻结';
//    $data[106] = '释放';
    // $data[107] = '话费充值';
    // $data[108] = '油卡充值';
    // $data[109] = '购买众筹';
//    $data[110] = '提现申请';
    // $data[131] = '充值';
    // $data[150] = '挂卖';
    // $data[151] = '挂买';
    // $data[152] = '商品购买';
    // $data[153] = '商品售出';
    // $data[154] = '互助出售';
    $data[0] = $data;

    return isset($data[intval($type)]) ? $data[intval($type)] : '';
}