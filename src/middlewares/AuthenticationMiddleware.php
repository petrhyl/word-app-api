<?php

namespace middlewares;

use WebApiCore\Http\HttpRequest;
use WebApiCore\Routes\Callables\IMiddleware;
use WebApiCore\Http\HttpUser;
use repository\user\UserRepository;
use services\user\auth\AuthService;

class AuthenticationMiddleware implements IMiddleware
{
    public const AUTH_HEADER_NAME = 'Authorization';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthService $authService
    ) {
    }

    public function invoke(HttpRequest $request, callable $next)
    {
        $headerValue = null;

        if (array_key_exists(self::AUTH_HEADER_NAME,$request->headers)) {
            $headerValue = $request->headers[self::AUTH_HEADER_NAME];
        }

        if (array_key_exists(strtolower(self::AUTH_HEADER_NAME),$request->headers)) {
            $headerValue = $request->headers[strtolower(self::AUTH_HEADER_NAME)];
        }

        if (empty($headerValue)) {
            return;
        }

        if (preg_match("/^Bearer\s+(.*)$/", $headerValue, $matches)) {
            $claims = $this->authService->getUserClaimsFromToken($matches[1]);

            if (empty($claims)) {
                return;
            }

            $user = new HttpUser();
            $user->id = $claims[AuthService::USER_ID_CLAIM];
            $user->email = $claims[AuthService::EMAIL_CLAIM];
            $user->apiKey = null;
            $user->token = $matches[1];

            $request->user = $user;
        }


        $next($request);
    }
}
