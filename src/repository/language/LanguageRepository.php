<?php

namespace repository\language;

use config\Database;
use models\domain\language\UserLanguage;
use PDO;

class LanguageRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database
    ) {
        $this->conn = $database->getConnection();
    }

    /**
     * @param int $userId
     * @return \models\domain\language\UserLanguage[]
     */
    public function getVacabularyLanguagesOfUser(int $userId): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM Wordapp_Languages WHERE UserId = :userId');

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_CLASS, UserLanguage::class);

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    public function getVocabularyLanguageOfUser(int $userId, string $languageCode): UserLanguage | null{
        $stmt = $this->conn->prepare('SELECT * FROM Wordapp_Languages WHERE UserId = :userId AND Code = :code');
        $stmt->execute(['userId' => $userId, 'code' => $languageCode]);
        
        $result = $stmt->fetchObject(UserLanguage::class);

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    public function createVocabularyLanguage(UserLanguage $userLanguage): bool{
        $stmt = $this->conn->prepare(
            'INSERT INTO Wordapp_Languages (UserId, Code, CorrectAnswers, IncorrectAnswers) VALUES (:userId, :code, :corrA, :incorrA)'
        );

        $stmt->bindValue(':userId', $userLanguage->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':code', $userLanguage->Code, PDO::PARAM_STR);
        $stmt->bindValue(':corrA', $userLanguage->CorrectAnswers, PDO::PARAM_INT);
        $stmt->bindValue(':incorrA', $userLanguage->IncorrectAnswers, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function updateVocabularyLanguage(UserLanguage $userLanguage): bool{
        $stmt = $this->conn->prepare(
            'UPDATE Wordapp_Languages SET CorrectAnswers = :corrA, IncorrectAnswers = :incorrA WHERE UserId = :userId AND Code = :code'
        );

        $stmt->bindValue(':corrA', $userLanguage->CorrectAnswers, PDO::PARAM_INT);
        $stmt->bindValue(':incorrA', $userLanguage->IncorrectAnswers, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userLanguage->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':code', $userLanguage->Code, PDO::PARAM_STR);

        return $stmt->execute();
    }
}