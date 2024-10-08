<?php

namespace mapping;

use models\domain\user\User;
use models\request\RegisterRequest;
use models\response\AuthResponse;

class UserMapper
{
    public static function mapToAuthResponse(User $user): AuthResponse
    {
        $response = new AuthResponse();
        $response->userId = $user->Id;
        $response->name = $user->Name;
        $response->email = $user->Email;
        $response->isVerified = $user->IsVerified;
        $response->accessToken = $user->AccessToken->Value;
        $response->accessTokenExpiresIn = $user->AccessToken->ExpireIn;
        $response->refreshToken = $user->RefreshToken->Value;
        $response->refreshTokenExpireIn = $user->RefreshToken->ExpireIn;

        return $response;
    }

    public static function mapRegisterRequestToUser(RegisterRequest $request): User
    {
        $user = new User();
        $user->Email = $request->email;
        $user->Name = $request->name;
        $user->Language = $request->language;

        return $user;
    }
}
