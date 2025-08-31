<?php

namespace repository\user;

use repository\database\Database;
use DateTime;
use models\domain\user\UserLogin;
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

    public function getById(int $tokenId): ?UserLogin
    {
        $cmd = "SELECT Id, UserId, TokenHash, ExpiresIn, CreatedAt FROM Wordapp_UserLogins WHERE Id = :id";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':id', $tokenId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchObject(UserLogin::class) ?: null;
    }

    /**
     * @return UserLogin|null ID of stored token or null on failure
     */
    public function store(UserLogin $userLogin): ?UserLogin
    {
        $cmd = "INSERT INTO Wordapp_UserLogins (UserId ,TokenHash, ExpiresIn)
                VALUES (:userId, :token, :expire)";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':userId', $userLogin->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':token', $userLogin->TokenHash, PDO::PARAM_STR);
        $stmt->bindValue(':expire', $userLogin->expiresAt()->format(Constants::DATABASE_DATETIME_FORMAT), PDO::PARAM_STR);

        $stmt->execute();
        if ($stmt->rowCount() === 0) {
            return null;
        }

        $id = $this->conn->lastInsertId();
        if (empty($id)) {
            return null;
        }

        $userLogin->Id = $id;

        return $userLogin;
    }

    public function delete(int $tokenId): bool
    {
        $cmd = "DELETE FROM Wordapp_UserLogins WHERE Id = :id";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':id', $tokenId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function deleteAllUserTokens(int $userId): bool
    {
        $cmd = "DELETE FROM Wordapp_UserLogins WHERE UserId = :user";

        $stmt = $this->conn->prepare($cmd);

        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function exists(int $tokenId, int $userId): bool
    {
        $query = "SELECT Id FROM Wordapp_UserLogins
                WHERE Id = :token AND UserId = :user";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':token', $tokenId, PDO::PARAM_INT);
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
