<?php

namespace endpoints;

use middlewares\AuthorizationMiddleware;
use WebApiCore\Routes\EndpointRouteBuilder;

class EndpointRegistrator
{
    public function __construct(private readonly EndpointRouteBuilder $router)
    {
    }

    public function registerEndpoints(): EndpointRouteBuilder
    {


        $this->router->post('blog/api/user/login', user\Login::class);
        $this->router->post('blog/api/user/logout', user\Logout::class, [AuthorizationMiddleware::class]);
        $this->router->post('blog/api/user/register', user\Register::class);
        $this->router->post('blog/api/user/refresh', user\Refresh::class);
        $this->router->post('blog/api/user/verification/{key}', user\Verification::class);

        return $this->router;
    }
}
