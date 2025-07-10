<?php

namespace repository\database;

class DbConfiguration
{
    private string $host;
    private string $database;
    private int $port;
    private string $user;
    private string $password;

    public function host(): string
    {
        return $this->host;
    }

    public function database(): string
    {
        return $this->database;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function user(): string
    {
        return  $this->user;
    }

    public function password(): string
    {
        return $this->password;
    }
}
