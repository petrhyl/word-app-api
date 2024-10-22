<?php

namespace utils;

class Constants
{    
    private static array $nonLatinLanguageCodes = ['am', 'ar', 'hy', 'be', 'bn', 'bg', 'zh', 'ka', 'el', 'gu', 'he', 'hi', 'ja', 
    'kn', 'kk', 'km', 'rw', 'ko', 'ku', 'ky', 'lo', 'mk', 'ml', 'mn', 'ms', 'my', 'ne', 
    'or', 'ps', 'fa', 'pa', 'ru', 'sr', 'sd', 'si', 'tg', 'ta', 'tt', 'te', 'th', 
    'ug', 'uk', 'uz', 'vi', 'xh', 'yi', 'yo', 'zu'];

    private static array $otherUnwantedLanguageCodes = ['af', 'az', 'ca', 'co', 'eu', 'ny', 'fy', 'gl', 'ha', 'ht', 'ig', 'jw', 'id', 'mb', 'mg', 'mi', 'mr', 'mt', 'st', 'sm', 'sn', 'so', 'su', 'sw', 'tk', 'tl', 'ur', 'cy'];
 
    public const DATABASE_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @return array of nested arrays with keys 'code' and 'name'
     */
    public static function allowedLanguages()
    {
        $allLanguages = self::allLanguages();
        $unwantedCodes = array_merge(self::$nonLatinLanguageCodes, self::$otherUnwantedLanguageCodes);
        
        return array_filter($allLanguages, fn($language) => !in_array($language['code'], $unwantedCodes));
    }

    /**
     * @return array of nested arrays with keys 'code' and 'name'
     */
    public static function allLanguages()
    {
        return [
            'af' => ['code' => 'af', 'name' => 'Afrikaans'],
            'sq' => ['code' => 'sq', 'name' => 'Albanian'],
            'am' => ['code' => 'am', 'name' => 'Amharic'],
            'ar' => ['code' => 'ar', 'name' => 'Arabic'],
            'hy' => ['code' => 'hy', 'name' => 'Armenian'],
            'az' => ['code' => 'az', 'name' => 'Azerbaijani'],
            'eu' => ['code' => 'eu', 'name' => 'Basque'],
            'be' => ['code' => 'be', 'name' => 'Belarusian'],
            'bn' => ['code' => 'bn', 'name' => 'Bengali'],
            'bs' => ['code' => 'bs', 'name' => 'Bosnian'],
            'bg' => ['code' => 'bg', 'name' => 'Bulgarian'],
            'ca' => ['code' => 'ca', 'name' => 'Catalan'],
            'ny' => ['code' => 'ny', 'name' => 'Chichewa'],
            'zh' => ['code' => 'zh', 'name' => 'Chinese'],
            'co' => ['code' => 'co', 'name' => 'Corsican'],
            'hr' => ['code' => 'hr', 'name' => 'Croatian'],
            'cs' => ['code' => 'cs', 'name' => 'Czech'],
            'da' => ['code' => 'da', 'name' => 'Danish'],
            'nl' => ['code' => 'nl', 'name' => 'Dutch'],
            'en' => ['code' => 'en', 'name' => 'English'],
            'eo' => ['code' => 'eo', 'name' => 'Esperanto'],
            'et' => ['code' => 'et', 'name' => 'Estonian'],
            'tl' => ['code' => 'tl', 'name' => 'Filipino'],
            'fi' => ['code' => 'fi', 'name' => 'Finnish'],
            'fr' => ['code' => 'fr', 'name' => 'French'],
            'fy' => ['code' => 'fy', 'name' => 'Frisian'],
            'gl' => ['code' => 'gl', 'name' => 'Galician'],
            'ka' => ['code' => 'ka', 'name' => 'Georgian'],
            'de' => ['code' => 'de', 'name' => 'German'],
            'el' => ['code' => 'el', 'name' => 'Greek'],
            'gu' => ['code' => 'gu', 'name' => 'Gujarati'],
            'ht' => ['code' => 'ht', 'name' => 'Haitian Creole'],
            'ha' => ['code' => 'ha', 'name' => 'Hausa'],
            'he' => ['code' => 'he', 'name' => 'Hebrew'],
            'hi' => ['code' => 'hi', 'name' => 'Hindi'],
            'hu' => ['code' => 'hu', 'name' => 'Hungarian'],
            'is' => ['code' => 'is', 'name' => 'Icelandic'],
            'ig' => ['code' => 'ig', 'name' => 'Igbo'],
            'id' => ['code' => 'id', 'name' => 'Indonesian'],
            'ga' => ['code' => 'ga', 'name' => 'Irish'],
            'it' => ['code' => 'it', 'name' => 'Italian'],
            'ja' => ['code' => 'ja', 'name' => 'Japanese'],
            'jw' => ['code' => 'jw', 'name' => 'Javanese'],
            'kn' => ['code' => 'kn', 'name' => 'Kannada'],
            'kk' => ['code' => 'kk', 'name' => 'Kazakh'],
            'km' => ['code' => 'km', 'name' => 'Khmer'],
            'rw' => ['code' => 'rw', 'name' => 'Kinyarwanda'],
            'ko' => ['code' => 'ko', 'name' => 'Korean'],
            'ku' => ['code' => 'ku', 'name' => 'Kurdish'],
            'ky' => ['code' => 'ky', 'name' => 'Kyrgyz'],
            'lo' => ['code' => 'lo', 'name' => 'Lao'],
            'la' => ['code' => 'la', 'name' => 'Latin'],
            'lv' => ['code' => 'lv', 'name' => 'Latvian'],
            'lt' => ['code' => 'lt', 'name' => 'Lithuanian'],
            'lb' => ['code' => 'lb', 'name' => 'Luxembourgish'],
            'mk' => ['code' => 'mk', 'name' => 'Macedonian'],
            'mg' => ['code' => 'mg', 'name' => 'Malagasy'],
            'ms' => ['code' => 'ms', 'name' => 'Malay'],
            'ml' => ['code' => 'ml', 'name' => 'Malayalam'],
            'mt' => ['code' => 'mt', 'name' => 'Maltese'],
            'mi' => ['code' => 'mi', 'name' => 'Maori'],
            'mr' => ['code' => 'mr', 'name' => 'Marathi'],
            'mn' => ['code' => 'mn', 'name' => 'Mongolian'],
            'my' => ['code' => 'my', 'name' => 'Myanmar (Burmese)'],
            'ne' => ['code' => 'ne', 'name' => 'Nepali'],
            'no' => ['code' => 'no', 'name' => 'Norwegian'],
            'or' => ['code' => 'or', 'name' => 'Odia (Oriya)'],
            'ps' => ['code' => 'ps', 'name' => 'Pashto'],
            'fa' => ['code' => 'fa', 'name' => 'Persian'],
            'pl' => ['code' => 'pl', 'name' => 'Polish'],
            'pt' => ['code' => 'pt', 'name' => 'Portuguese'],
            'pa' => ['code' => 'pa', 'name' => 'Punjabi'],
            'ro' => ['code' => 'ro', 'name' => 'Romanian'],
            'ru' => ['code' => 'ru', 'name' => 'Russian'],
            'sm' => ['code' => 'sm', 'name' => 'Samoan'],
            'gd' => ['code' => 'gd', 'name' => 'Scots Gaelic'],
            'sr' => ['code' => 'sr', 'name' => 'Serbian'],
            'st' => ['code' => 'st', 'name' => 'Sesotho'],
            'sn' => ['code' => 'sn', 'name' => 'Shona'],
            'sd' => ['code' => 'sd', 'name' => 'Sindhi'],
            'si' => ['code' => 'si', 'name' => 'Sinhala'],
            'sk' => ['code' => 'sk', 'name' => 'Slovak'],
            'sl' => ['code' => 'sl', 'name' => 'Slovenian'],
            'so' => ['code' => 'so', 'name' => 'Somali'],
            'es' => ['code' => 'es', 'name' => 'Spanish'],
            'su' => ['code' => 'su', 'name' => 'Sundanese'],
            'sw' => ['code' => 'sw', 'name' => 'Swahili'],
            'sv' => ['code' => 'sv', 'name' => 'Swedish'],
            'tg' => ['code' => 'tg', 'name' => 'Tajik'],
            'ta' => ['code' => 'ta', 'name' => 'Tamil'],
            'tt' => ['code' => 'tt', 'name' => 'Tatar'],
            'te' => ['code' => 'te', 'name' => 'Telugu'],
            'th' => ['code' => 'th', 'name' => 'Thai'],
            'tr' => ['code' => 'tr', 'name' => 'Turkish'],
            'tk' => ['code' => 'tk', 'name' => 'Turkmen'],
            'uk' => ['code' => 'uk', 'name' => 'Ukrainian'],
            'ur' => ['code' => 'ur', 'name' => 'Urdu'],
            'ug' => ['code' => 'ug', 'name' => 'Uyghur'],
            'uz' => ['code' => 'uz', 'name' => 'Uzbek'],
            'vi' => ['code' => 'vi', 'name' => 'Vietnamese'],
            'cy' => ['code' => 'cy', 'name' => 'Welsh'],
            'xh' => ['code' => 'xh', 'name' => 'Xhosa'],
            'yi' => ['code' => 'yi', 'name' => 'Yiddish'],
            'yo' => ['code' => 'yo', 'name' => 'Yoruba'],
            'zu' => ['code' => 'zu', 'name' => 'Zulu']
        ];
    }

    /**
     * @return array of nested arrays with keys 'code' and 'name'
     */
    public static function latinLanguages(): array
    {
        $allLanguages = self::allLanguages();
        
        return array_filter($allLanguages, fn($language) => !in_array($language['code'], self::$nonLatinLanguageCodes));
    }

    public static function rootDir(): string
    {
        return dirname(dirname(__DIR__));
    }
}
