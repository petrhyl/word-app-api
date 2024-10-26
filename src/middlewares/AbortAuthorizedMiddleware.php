<?php

namespace middlewares;

use WebApiCore\Exceptions\ApplicationException;
use WebApiCore\Http\HttpRequest;
use WebApiCore\Routes\Callables\IMiddleware;

class AbortAuthorizedMiddleware implements IMiddleware
{
    public function invoke(HttpRequest $request, callable $next)
    {
        if (!empty($request->user->id)) {
            throw new ApplicationException("Not allowed for authorized user.", 403);
        }

        $next($request);
    }
}
