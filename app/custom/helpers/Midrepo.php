<?php

namespace custom\helpers;

class Midrepo {

    public static $thereIsMail = false;

    private static $repo =[];

    public static $apiLogin = false;

    public static $test;
    public static function all()
    {
        return static::$repo;
    }

    public static function add($key, $value)
    {
        static::$repo[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if(array_key_exists($key, self::$repo))
        {
            return static::$repo[$key];
        } else {
            return $default;
        }
    }

    public static function getOrFail($key)
    {
        $res = static::get($key);
        if(!isset($res))
        {

           \App::abort(500, "'$key' not found in mid repo");
        }

        return $res;
    }

    public static function has($key)
    {
        return array_key_exists($key, self::$repo);
    }


}