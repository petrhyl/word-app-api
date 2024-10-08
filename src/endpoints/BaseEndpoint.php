<?php

namespace endpoints;

use WebApiCore\Http\HttpResponse;

class BaseEndpoint
{
    protected function respond(mixed $data, int $statusCode = 200, array $errors = null): void
    {
        $response = new HttpResponse($data, $statusCode, $errors);
        $response->send();
    }

    protected function respondAndDie(mixed $data, int $statusCode = 200, array $errors = null): void
    {
        $this->respond($data, $statusCode, $errors);
        die();
    }
}
