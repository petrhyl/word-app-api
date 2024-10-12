<?php

namespace mapping;

use models\domain\user\User;
use models\request\RegisterRequest;
use models\response\AuthResponse;
use models\response\TokenResponse;
use models\response\UserResponse;

class UserMapper
{
    public static function mapToAuthResponse(User $user): AuthResponse
    {
        $response = new AuthResponse();
        $response->user = self::mapToUserResponse($user);
        $response->token = self::mapToTokenResponse($user);

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

    public static function mapToUserResponse(User $user) : UserResponse
    {
        $response = new UserResponse();
        $response->id = $user->Id;
        $response->name = $user->Name;
        $response->email = $user->Email;
        $response->isVerified = $user->IsVerified;

        return $response;   
    }

    public static function mapToTokenResponse(User $user) : TokenResponse
    {
        $response = new TokenResponse();
        $response->accessToken = $user->AccessToken->Value;
        $response->accessTokenExpiresIn = $user->AccessToken->ExpireIn;
        $response->refreshToken = $user->RefreshToken->Value;
        $response->refreshTokenExpireIn = $user->RefreshToken->ExpireIn;

        return $response;
    }
}
