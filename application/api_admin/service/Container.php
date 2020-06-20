<?php

namespace app\api_admin\service;


class Container
{
    /**
     * 容器中的对象实例
     * @var array
     */
    protected static $instances = [];

    public static function set($name, $instance)
    {
        self::$instances[$name] = $instance;
    }

    public static function has($name)
    {
        return isset(self::$instances[$name]) ? true : false;
    }

    public static function get($name)
    {
        return self::$instances[$name];
    }
}
