<?php

namespace middlewares;

use WebApiCore\Exceptions\ApplicationException;
use WebApiCore\Http\HttpRequest;
use WebApiCore\Routes\Callables\IMiddleware;

class AuthorizationMiddleware implements IMiddleware
{
    public function __construct()
    {
    }

    public function invoke(HttpRequest $request, callable $next)
    {       
        if (empty($request->user) || empty($request->user->token)) {
            throw new ApplicationException("User is not authenticated", 401);
        }

        $next($request);
    }
}
