<?php

namespace utils;

class Utils
{
    public static function dd(mixed $data): void
    {
        $json = json_encode(['exam' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
        echo $json;
    }
}
