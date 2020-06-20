<?php

namespace app\common\model;

/**
 * 后台数据统计
 * Class AdminStatistics
 * @package app\common\model
 */
class AdminStatistics
{

    /**
     * 获取今日收入
     * @return int
     */
    public static function getTodayIncome()
    {
        return 11;
    }

    /**
     * 获取总收入
     * @return int
     */
    public static function getTotalIncome()
    {
        return 111;
    }

    /**
     * 获取今日支出
     * @return int
     */
    public static function getTodayExpenses()
    {
        return 22;
    }

    /**
     * 获取总支出
     * @return int
     */
    public static function getTotalExpenses()
    {
        return 222;
    }
}