<?php

namespace endpoints\exercises;

use endpoints\BaseEndpoint;
use models\request\GetExerciseQuery;
use services\exercise\ExerciseService;
use validators\exercise\GetExerciseQueryValidator;

class GetExercise extends BaseEndpoint
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
        private readonly GetExerciseQueryValidator $validator
    ) {}

    public function __invoke(GetExerciseQuery $query)
    {
        $this->validator->validate($query);

        $response = $this->exerciseService->getExercise($query);

        $this->respondAndDie(['exercise' => $response]);
    }
}
