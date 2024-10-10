<?php

namespace models\response;

class RegisterResponse
{
    public ?AuthResponse $auth;
    public ?RegisterResponseMessage $registration;
}