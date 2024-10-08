<?php

namespace services;

class EncryptionService
{
    public function __construct(private readonly string $secretKey)
    {
    }

    public function hashWithSecret(string $text, bool $getBinary = true): string
    {
        $key = bin2hex($this->secretKey);

        return hash_hmac(
            'sha256',
            $text,
            $key,
            $getBinary
        );
    }

    public function base64urlEncode(string $text): string
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    public function base64urlDecode(string $text): string
    {
        return base64_decode(str_replace(
            ['-', '_'],
            ['+', '/'],
            $text
        ));
    }
}
