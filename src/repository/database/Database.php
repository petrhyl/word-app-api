<?php

namespace repository\database;

use models\DbConfiguration;
use PDO;

class Database
{
    private ?PDO $connection;

    public function __construct(
        private DbConfiguration $config
    ) {
        $this->connection = null;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->config->host()};dbname={$this->config->database()};port={$this->config->port()}charset=utf8mb4";

            $this->connection = new PDO($dsn, $this->config->user(), $this->config->password(), [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        }

        return $this->connection;
    }
}
