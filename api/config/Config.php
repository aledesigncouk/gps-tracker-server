<?php

/*
DB_HOST
DB_USERNAME
DB_PASSWORD
DB_NAME
DB_TABLE_NAME
API_KEY
*/

namespace Alex\GpsTrackerServer\config;

$env = parse_ini_file(__DIR__ . '/../../.env');

class Config
{
    private static $env;

    public static function load()
    {
        if (self::$env === null) {
            self::$env = parse_ini_file(__DIR__ . '/../../.env');
        }
    }

    public static function get($key, $default = null)
    {
        self::load();
        return self::$env[$key] ?? $default;
    }
}
