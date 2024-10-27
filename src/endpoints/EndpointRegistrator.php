<?php

namespace endpoints;

use middlewares\AbortAuthorizedMiddleware;
use middlewares\AuthorizationMiddleware;
use WebApiCore\Routes\EndpointRouteBuilder;

class EndpointRegistrator
{
    public function __construct(private readonly EndpointRouteBuilder $router) {}

    public function registerEndpoints(): EndpointRouteBuilder
    {
        $this->router->get('word-app/api/user/auth', user\GetAuthenticatedUser::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/login', user\Login::class, [AbortAuthorizedMiddleware::class]);
        $this->router->post('word-app/api/user/logout', user\Logout::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/register', user\Register::class, [AbortAuthorizedMiddleware::class]);
        $this->router->post('word-app/api/user/refresh', user\Refresh::class);
        $this->router->post('word-app/api/user/verify/{key}', user\Verification::class, [AbortAuthorizedMiddleware::class]);
        $this->router->post('word-app/api/user/send', user\SendVerificationEmail::class, [AbortAuthorizedMiddleware::class]);
        $this->router->put('word-app/api/user/change-password', user\ChangePassword::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/user/forget-password', user\ForgetPassword::class, [AbortAuthorizedMiddleware::class]);
        $this->router->put('word-app/api/user/reset-password', user\ResetPassword::class, [AbortAuthorizedMiddleware::class]);

        $this->router->get('word-app/api/vocabularies/{langId}', vocabularies\GetLanguageVocabulary::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/vocabularies', vocabularies\CreateUserVocabulary::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/vocabularies/check', vocabularies\CheckIfWordExists::class, [AuthorizationMiddleware::class]);
        $this->router->put('word-app/api/vocabularies/items/{id}', vocabularies\UpdateUserVocabularyItem::class, [AuthorizationMiddleware::class]);

        $this->router->get('word-app/api/exercises', exercises\GetExercise::class, [AuthorizationMiddleware::class]);
        $this->router->get('word-app/api/exercises/results', exercises\GetResults::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/exercises/results', exercises\CreateExerciseResult::class, [AuthorizationMiddleware::class]);

        $this->router->get('word-app/api/languages/allowed', languages\GetAllowedLanguages::class);
        $this->router->get('word-app/api/languages/user', languages\GetUserVocabularyLanguages::class, [AuthorizationMiddleware::class]);
        $this->router->post('word-app/api/languages/{code}', languages\CreateUserVocabularyLanguage::class, [AuthorizationMiddleware::class]);
        $this->router->delete('word-app/api/languages/{id}', languages\DeleteUserVocabularyLanguage::class, [AuthorizationMiddleware::class]);

        return $this->router;
    }
}
