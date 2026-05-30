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

class Config
{
    private static $env;

    public static function load()
    {
        if (!is_array(self::$env)) {
            self::$env = parse_ini_file(__DIR__ . '/../../.env');
            if (self::$env === false) {
                throw new \RuntimeException('Failed to load .env configuration file.');
            }
        }
    }

    public static function get($key, $default = null)
    {
        self::load();
        return self::$env[$key] ?? $default;
    }
}
