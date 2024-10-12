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
        $this->router->get('word-app/api/user/auth', user\GetAuthenticatedUser::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/login', user\Login::class);
        $this->router->post('word-app/api/user/logout', user\Logout::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/register', user\Register::class);
        $this->router->post('word-app/api/user/refresh', user\Refresh::class);
        $this->router->post('word-app/api/user/verification/{key}', user\Verification::class);

        $this->router->post('word-app/api/vocabulary', vocabulary\CreateUserVocabulary::class, [AuthorizationMiddleware::class]);
        $this->router->get('word-app/api/vocabulary', vocabulary\GetUserVocabulary::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/vocabulary/items/{id}', vocabulary\UpdateUserVocabularyItem::class, [AuthorizationMiddleware::class]);

        $this->router->post('word-app/api/vocabulary/items/{id}', languages\GetUserLanguages::class, [AuthorizationMiddleware::class]);

        return $this->router;
    }
}
