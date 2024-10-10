<?php

namespace repository\user;

use config\Database;
use DateTime;
use PDO;
use utils\Constants;

class TokenRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database
    ) {
        $this->conn = $database->getConnection();
    }

    public function store(int $userId, string $tokenHashed, DateTime $expiry): bool
    {
        $cmd = "INSERT INTO Wordapp_UserLogins (UserId ,TokenHash, ExpiresIn)
                VALUES (:id, :token, :expire)";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':token', $tokenHashed, PDO::PARAM_STR);
        $stmt->bindValue(':expire', $expiry->format(Constants::DATABASE_DATETIME_FORMAT), PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function delete(string $tokenHashed, int $userId): bool
    {
        $cmd = "DELETE FROM Wordapp_UserLogins WHERE TokenHash = :token AND UserId = :user";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':token', $tokenHashed, PDO::PARAM_STR);
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function exists(string $tokenHashed, int $userId): bool
    {
        $query = "SELECT Id FROM Wordapp_UserLogins
                WHERE TokenHash = :token AND UserId = :user";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':token', $tokenHashed, PDO::PARAM_STR);
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch();

        return !empty($result) && !empty($result['Id']);
    }

    public function deleteExpired(): int
    {
        $cmd = "DELETE FROM Wordapp_UserLogins
                WHERE ExpiresIn < UNIX_TIMESTAMP()";

        $stmt = $this->conn->query($cmd);

        return $stmt->rowCount();
    }
}
