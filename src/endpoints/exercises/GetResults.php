<?php

namespace endpoints\exercises;

use endpoints\BaseEndpoint;
use services\exercise\ExerciseService;

class GetResults extends BaseEndpoint{
    public function __construct(private readonly ExerciseService $exerciseService){ 
        
    }

    public function __invoke()
    {
        $response = $this->exerciseService->getUserResults();

        $this->respondAndDie(["results" => $response]);
    }
}