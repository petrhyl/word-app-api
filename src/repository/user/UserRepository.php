<?php

namespace repository\user;

use config\Database;
use DateTime;
use models\domain\user\User;
use PDO;

class UserRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database
    ) {
        $this->conn = $database->getConnection();
    }

    private const GET_USER_QUERY =
    "SELECT us.Id AS usid, us.Email AS email, us.Name AS usname, us.PasswordHash AS uspswd, us.VerificationKey AS uskey, 
    us.IsVerified AS isver, us.Language AS lang, us.UpdatedAt AS usup, us.CreatedAt AS uscr
    FROM Wordapp_Users AS us
    ";


    public function create(User $user): ?User
    {
        $command = "INSERT INTO Wordapp_Users (Email, Name, PasswordHash, IsVerified, VerificationKey, Language)
        VALUES (:email, :name, :psswd, :isver, :verkey, :lang)";

        $stmt = $this->conn->prepare($command);
        $stmt->bindValue(':email', $user->Email, PDO::PARAM_STR);
        $stmt->bindValue(':name', $user->Name, PDO::PARAM_STR);
        $stmt->bindValue(':psswd', $user->PasswordHash, PDO::PARAM_STR);
        $stmt->bindValue(':isver', $user->IsVerified, PDO::PARAM_BOOL);
        $stmt->bindValue(':verkey', $user->VerificationKey, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':lang', $user->Language, PDO::PARAM_STR);

        $stmt->execute();

        $id = $this->conn->lastInsertId();

        if (empty($id)) {
            return null;
        }

        $user->Id = $id;

        return $user;
    }

    public function update(User $user): bool
    {
        $command = "UPDATE Wordapp_Users
        SET Email = :email, Name = :name, PasswordHash = :psswd, UpdatedAt = :up, IsVerified = :isver, VerificationKey = :verkey, Language = :lang
        WHERE Id = :id";

        $stmt = $this->conn->prepare($command);
        $stmt->bindValue(':email', $user->Email, PDO::PARAM_STR);
        $stmt->bindValue(':name', $user->Name, PDO::PARAM_STR);
        $stmt->bindValue(':psswd', $user->PasswordHash, PDO::PARAM_STR);
        $stmt->bindValue(':up', $user->mysqlFormattedUpdatedAt(), PDO::PARAM_STR);
        $stmt->bindValue(':isver', $user->IsVerified, PDO::PARAM_BOOL);
        $stmt->bindValue(':verkey', $user->VerificationKey, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindValue(':lang', $user->Language, PDO::PARAM_STR);
        $stmt->bindValue(':id', $user->Id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getById(int $id): ?User
    {
        return $this->getByValue($id, 'Id');
    }

    /**
     * converts the email to lowercase before searching
     */
    public function getByEmail(string $email): ?User
    {
        return $this->getByValue(strtolower($email), 'Email');
    }

    public function getByVerificationKey(string $verificationKey): ?User
    {
        return $this->getByValue($verificationKey, 'VerificationKey');
    }

    public function delete(User $user): bool
    {
        $command = "DELETE FROM Wordapp_Users WHERE Id = :id";

        $stmt = $this->conn->prepare($command);
        $stmt->bindValue(':id', $user->Id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }


    private function getByValue(string|int $fieldValue, string $fieldName): ?User
    {
        $query = self::GET_USER_QUERY . " WHERE us.{$fieldName} = :srch";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':srch', $fieldValue, $fieldName === 'Id' ? PDO::PARAM_INT : PDO::PARAM_STR);

        $stmt->execute();

        $data = $stmt->fetch();

        if (empty($data)) {
            return null;
        }

        $user = $this->convertSqlDataToUser($data);

        return $user;
    }

    private function convertSqlDataToUser(array $userData): User
    {
        $user = new User();

        $user->Id = $userData['usid'];
        $user->Email = $userData['email'];
        $user->Name = $userData['usname'];
        $user->PasswordHash = $userData['uspswd'];
        $user->IsVerified = $userData['isver'];
        $user->VerificationKey = $userData['uskey'];
        $user->Language = $userData['lang'];
        $user->UpdatedAt = new DateTime($userData['usup']);
        $user->setCreatedAt(new DateTime($userData['uscr']));

        return $user;
    }
}
