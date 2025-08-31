<?php

namespace models\response;

class AuthResponse
{    
    public UserResponse $user;
    public TokenResponse $authToken;
}
