<?php

namespace services\user\auth;

use services\EncryptionService;

class TokenService
{
    public function __construct(private readonly EncryptionService $encryption)
    {
    }

    public function generateJwtToken(array $claims): string
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $header = $this->encryption->base64urlEncode($header);

        $stringyfiedClaims = json_encode($claims);

        $stringyfiedClaims = $this->encryption->base64urlEncode($stringyfiedClaims);

        $signiture = $this->encryption->hashWithSecret($header . '.' . $stringyfiedClaims);

        $signiture = $this->encryption->base64urlEncode($signiture);

        return $header . '.' . $stringyfiedClaims . '.' . $signiture;
    }

    public function generateRefreshToken() : string {
        return $this->encryption->base64urlEncode(random_bytes(64));
    }

    public function decodeDataFromToken(string $token): ?array
    {
        if (!preg_match(
            '/^(?<header>.+)\.(?<identity>.+)\.(?<signature>.+)$/',
            $token,
            $matches
        )) {
            return null;
        }        

        $signiture = $this->encryption->hashWithSecret($matches['header'] . '.' . $matches['identity']);

        $signitureFromToken = $this->encryption->base64urlDecode($matches['signature']);

        if (!hash_equals($signiture, $signitureFromToken)) {
            return null;
        }

        $claims = json_decode($this->encryption->base64urlDecode($matches['identity']), true);

        return $claims;
    }
}
