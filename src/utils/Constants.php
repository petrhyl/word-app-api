<?php

namespace utils;

class Constants
{
    public const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public static function rootDir(): string
    {
        return dirname(dirname(__DIR__));
    }
}
