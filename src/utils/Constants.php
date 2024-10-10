<?php

namespace utils;

class Constants
{
    public const DATABASE_DATETIME_FORMAT = 'Y-m-d H:i:s';
    
    public static function allowedLanguages()
    {
        return [
            'en' => 'en',
            'cs' => 'cs'
        ];
    }

    public static function languageCodes(){
        return [
            'af' => 'af', 'sq' => 'sq', 'am' => 'am', 'ar' => 'ar', 'hy' => 'hy', 'az' => 'az', 'eu' => 'eu', 'be' => 'be', 
            'bn' => 'bn', 'bs' => 'bs', 'bg' => 'bg', 'ca' => 'ca', 'ny' => 'ny', 'zh' => 'zh', 'co' => 'co', 
            'hr' => 'hr', 'cs' => 'cs', 'da' => 'da', 'nl' => 'nl', 'en' => 'en', 'eo' => 'eo', 'et' => 'et', 'tl' => 'tl', 
            'fi' => 'fi', 'fr' => 'fr', 'fy' => 'fy', 'gl' => 'gl', 'ka' => 'ka', 'de' => 'de', 'el' => 'el', 'gu' => 'gu', 
            'ht' => 'ht', 'ha' => 'ha', 'he' => 'he', 'hi' => 'hi', 'hu' => 'hu', 'is' => 'is', 
            'ig' => 'ig', 'id' => 'id', 'ga' => 'ga', 'it' => 'it', 'ja' => 'ja', 'jw' => 'jw', 'kn' => 'kn', 'kk' => 'kk', 
            'km' => 'km', 'rw' => 'rw', 'ko' => 'ko', 'ku' => 'ku', 'ky' => 'ky', 'lo' => 'lo', 'la' => 'la', 'lv' => 'lv', 
            'lt' => 'lt', 'lb' => 'lb', 'mk' => 'mk', 'mg' => 'mg', 'ms' => 'ms', 'ml' => 'ml', 'mt' => 'mt', 'mi' => 'mi', 
            'mr' => 'mr', 'mn' => 'mn', 'my' => 'my', 'ne' => 'ne', 'no' => 'no', 'or' => 'or', 'ps' => 'ps', 'fa' => 'fa', 
            'pl' => 'pl', 'pt' => 'pt', 'pa' => 'pa', 'ro' => 'ro', 'ru' => 'ru', 'sm' => 'sm', 'gd' => 'gd', 'sr' => 'sr', 
            'st' => 'st', 'sn' => 'sn', 'sd' => 'sd', 'si' => 'si', 'sk' => 'sk', 'sl' => 'sl', 'so' => 'so', 'es' => 'es', 
            'su' => 'su', 'sw' => 'sw', 'sv' => 'sv', 'tg' => 'tg', 'ta' => 'ta', 'tt' => 'tt', 'te' => 'te', 'th' => 'th', 
            'tr' => 'tr', 'tk' => 'tk', 'uk' => 'uk', 'ur' => 'ur', 'ug' => 'ug', 'uz' => 'uz', 'vi' => 'vi', 'cy' => 'cy', 
            'xh' => 'xh', 'yi' => 'yi', 'yo' => 'yo', 'zu' => 'zu'
        ];
    }

    public static function rootDir(): string
    {
        return dirname(dirname(__DIR__));
    }
}
