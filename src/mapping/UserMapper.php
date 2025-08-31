<?php

namespace mapping;

use models\domain\user\User;
use models\request\RegisterRequest;
use models\response\AuthResponse;
use models\response\TokenResponse;
use models\response\UserResponse;

class UserMapper
{
    public static function mapToAuthResponse(User $user, TokenResponse $tokenResponse): AuthResponse
    {
        $response = new AuthResponse();
        $response->user = self::mapToUserResponse($user);
        $response->authToken = $tokenResponse;

        return $response;
    }

    public static function mapRegisterRequestToUser(RegisterRequest $request, string $passwordHash): User
    {
        $user = new User();
        $user->Email = $request->email;
        $user->Name = $request->name;
        $user->PasswordHash = $passwordHash;
        $user->IsVerified = false;
        $user->Language = $request->language;

        return $user;
    }

    public static function mapToUserResponse(User $user): UserResponse
    {
        $response = new UserResponse();
        $response->id = $user->Id;
        $response->name = $user->Name;
        $response->email = $user->Email;
        $response->isVerified = $user->IsVerified;

        return $response;
    }
}
