<?php

namespace repository\language;

use config\Database;
use models\domain\language\VocabularyLanguage;
use PDO;

class LanguageRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database
    ) {
        $this->conn = $database->getConnection();
    }

    private const GET_QUERY = "SELECT * FROM Wordapp_VocabularyLanguages";

    public function getVocabularyLanguageById(int $id): VocabularyLanguage | null
    {
        $stmt = $this->conn->prepare(self::GET_QUERY . " WHERE Id = :id");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchObject(VocabularyLanguage::class);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * @param int $userId
     * @return \models\domain\language\VocabularyLanguage[]
     */
    public function getVacabularyLanguagesOfUser(int $userId): array
    {
        $stmt = $this->conn->prepare(self::GET_QUERY . " WHERE UserId = :userId");

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS, VocabularyLanguage::class);

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    public function getVocabularyLanguageOfUser(int $userId, string $languageCode): VocabularyLanguage | null
    {
        $stmt = $this->conn->prepare(self::GET_QUERY . " WHERE UserId = :userId AND Code = :code");

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':code', $languageCode, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetchObject(VocabularyLanguage::class);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    public function createVocabularyLanguage(VocabularyLanguage $userLanguage): VocabularyLanguage | null
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO Wordapp_VocabularyLanguages (UserId, Code) VALUES (:userId, :code)"
        );

        $stmt->bindValue(':userId', $userLanguage->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':code', $userLanguage->Code, PDO::PARAM_STR);

        $stmt->execute();

        $id = $this->conn->lastInsertId();

        if (empty($id)) {
            return null;
        }

        $userLanguage->Id = (int) $id;

        return $userLanguage;
    }

    public function deleteVocabularyLanguage(int $languageId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM Wordapp_VocabularyLanguages WHERE Id = :id");

        $stmt->bindValue(':id', $languageId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
