<?php

namespace models\response;

class UserResponse{
    public int $id;
    public string $name;
    public string $email;
    public bool $isVerified;
}