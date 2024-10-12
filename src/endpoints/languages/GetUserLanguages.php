<?php

namespace endpoints\languages;

use endpoints\BaseEndpoint;
use services\language\LanguagesService;

class GetUserLanguages extends BaseEndpoint
{
    public function __construct(private readonly LanguagesService $service)
    {
    }

    public function __invoke()
    {
        
    }
}