<?php

namespace endpoints\exercises;

use endpoints\BaseEndpoint;
use models\request\CreateExerciseResultRequest;
use services\exercise\ExerciseService;
use validators\exercise\CreateExerciseResultRequestValidator;

class CreateExerciseResult extends BaseEndpoint
{
    public function __construct(
        private readonly ExerciseService $exerciseService,
        private readonly CreateExerciseResultRequestValidator $validator
    ) {}

    public function __invoke(CreateExerciseResultRequest $payload)
    {
        $this->validator->validate($payload);

        $this->exerciseService->createExerciseResult($payload);

        $this->respondAndDie(['message' => 'Exercise result was successfully created.']);
    }
}
