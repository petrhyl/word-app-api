<?php

namespace endpoints;

use middlewares\AuthorizationMiddleware;
use WebApiCore\Routes\EndpointRouteBuilder;

class EndpointRegistrator
{
    public function __construct(private readonly EndpointRouteBuilder $router) {}

    public function registerEndpoints(): EndpointRouteBuilder
    {
        $this->router->get('word-app/api/user/auth', user\GetAuthenticatedUser::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/login', user\Login::class);
        $this->router->post('word-app/api/user/logout', user\Logout::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/register', user\Register::class);
        $this->router->post('word-app/api/user/refresh', user\Refresh::class);
        $this->router->post('word-app/api/user/verification/{key}', user\Verification::class);

        $this->router->post('word-app/api/vocabulary', vocabulary\CreateUserVocabulary::class, [AuthorizationMiddleware::class]);
        $this->router->post(
            'word-app/api/vocabulary/items/{id}',
            vocabulary\UpdateUserVocabularyItemTranslations::class,
            [AuthorizationMiddleware::class]
        );

        $this->router->get('word-app/api/exercises', exercises\GetExercise::class, [AuthorizationMiddleware::class]);
        $this->router->get('word-app/api/exercises/results', exercises\GetResults::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/exercises/results', exercises\CreateExerciseResult::class, [AuthorizationMiddleware::class]);

        $this->router->get('word-app/api/languages/allowed', languages\GetAllowedLanguages::class);
        $this->router->get('word-app/api/languages/user', languages\GetUserVocabularyLanguages::class, [AuthorizationMiddleware::class]);

        return $this->router;
    }
}
