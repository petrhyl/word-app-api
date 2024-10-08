<?php

namespace validators\common;

class ValidatorUtils
{
    private const NAME_REGEX =
    "/^[A-ZŠŽČŘĎŤŇŸĹĽŔŚŹĆŃÀ-ÖÙ-Ýa-zšžřčťďňěľĺŕůśźćńà-ïñ-öù-ÿ]+([ \-']{1}[A-ZŠŽČŘĎŤŇŸĹĽŔŚŹĆŃÀ-ÖÙ-Ýa-zšžřčťďňěľĺŕůśźćńà-ïñ-öù-ÿ]+)*$/";
    private const HOUSE_REGEX = '/^\d+[A-Za-z]?([\s \-\/]{1}\d+[A-Za-z]?){0,3}$/';
    private const ZIP_REGEX = '/^\d{5}(-\d{4})?$/';
    private const PHONE_REGEX = '/^\+{1}\d{7,17}$/';


    public static function isNameValid(string $value): bool
    {
        return preg_match(self::NAME_REGEX, $value) === 1;
    }

    public static function isEmailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isHouseNumberValid(string $value): bool
    {
        return preg_match(self::HOUSE_REGEX, $value) === 1;
    }

    public static function isPostalCodeValid(string $value): bool
    {
        return preg_match(self::ZIP_REGEX, $value) === 1;
    }

    public static function isInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function isValidFloat($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function isPhoneNumberValid(string $value): bool
    {
        return preg_match(self::PHONE_REGEX, $value) === 1;
    }
}
