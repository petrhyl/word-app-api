<?php

namespace repository\vocabulary;

use config\Database;
use models\domain\vocabulary\VocabularyItem;
use PDO;

class VocabularyRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database
    ) {
        $this->conn = $database->getConnection();
    }

    private const GET_WORD_QUERY = "SELECT * FROM Wordapp_Vocabularies";
    private const ORDER_BY_QUERY = " ORDER BY UpdatedAt ASC, CorrectAnswers ASC";


    /**
     * @return VocabularyItem[]
     */
    public function getUserVocabulary(int $userId, int $languageId, int $limit, int $offset) : array
    {
        $query = self::GET_WORD_QUERY . 
        " WHERE UserId = :user AND VocabularyLanguageId = :lang 
        ORDER BY Value ASC
        LIMIT :lim OFFSET :off";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lang', $languageId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS, VocabularyItem::class);

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @return VocabularyItem[]
     */
    public function getUserUnlearnedVocabulary(int $userId, int $languageId, int $limit): array
    {
        $query = self::GET_WORD_QUERY .
            " WHERE UserId = :user AND VocabularyLanguageId = :lang AND IsLearned = 0" .
            self::ORDER_BY_QUERY .
            " LIMIT :lim";

        return $this->getVocabulary($userId, $languageId, $query, $limit);
    }

    /**
     * @return VocabularyItem[]
     */
    public function getUserLearnedVocabulary(int $userId, int $language, int $limit): array
    {
        $query = self::GET_WORD_QUERY .
            " WHERE UserId = :user AND VocabularyLanguageId = :lang AND IsLearned = 1" .
            self::ORDER_BY_QUERY
            . " LIMIT :lim";

        return $this->getVocabulary($userId, $language, $query, $limit);
    }

    public function getVocabularyItem(int $id): VocabularyItem | null
    {
        $stmt = $this->conn->prepare(self::GET_WORD_QUERY . " WHERE Id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchObject(VocabularyItem::class);

        return empty($result) ? null : $result;
    }

    public function getUserVocabularyItem(int $userId, int $languageId, string $word): VocabularyItem | null
    {
        $stmt = $this->conn->prepare(self::GET_WORD_QUERY . " WHERE UserId = :user AND VocabularyLanguageId = :lang AND Value = :val");

        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lang', $languageId, PDO::PARAM_INT);
        $stmt->bindValue(':val', $word, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetchObject(VocabularyItem::class);

        return empty($result) ? null : $result;
    }

    /**
     * @param VocabularyItem[] $vocabularyItems
     */
    public function createVocabulary(array $vocabularyItems): bool
    {
        $command = "INSERT INTO Wordapp_Vocabularies (UserId, VocabularyLanguageId, Value, Translations) 
        VALUES ";

        foreach ($vocabularyItems as $key => $value) {
            $command .= "(:user{$key}, :lang{$key}, :val{$key}, :trans{$key}),\n";
        }

        $command = rtrim($command, ",\n");

        $stmt = $this->conn->prepare($command);

        foreach ($vocabularyItems as $key => $value) {
            $stmt->bindValue(":user{$key}", $value->UserId, PDO::PARAM_INT);
            $stmt->bindValue(":lang{$key}", $value->VocabularyLanguageId, PDO::PARAM_INT);
            $stmt->bindValue(":val{$key}", $value->Value, PDO::PARAM_STR);
            $stmt->bindValue(":trans{$key}", $value->Translations, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public function createVocabularyItem(VocabularyItem $vocabularyItem): bool
    {
        $command = "INSERT INTO Wordapp_Vocabularies (UserId, VocabularyLanguageId, Value, Translations)
        VALUES (:user, :lang, :val, :trans)";

        $stmt = $this->conn->prepare($command);

        $stmt->bindValue(':user', $vocabularyItem->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':lang', $vocabularyItem->VocabularyLanguageId, PDO::PARAM_INT);
        $stmt->bindValue(':val', $vocabularyItem->Value, PDO::PARAM_STR);
        $stmt->bindValue(':trans', $vocabularyItem->Translations, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function updateVocabularyItem(VocabularyItem $vocabularyItem): bool
    {
        $command = "UPDATE Wordapp_Vocabularies 
        SET Translations = :trans, IsLearned = :lear, CorrectAnswers = :cor, UpdatedAt = :up 
        WHERE Id = :id";

        $stmt = $this->conn->prepare($command);

        $stmt->bindValue(':trans', $vocabularyItem->Translations, PDO::PARAM_STR);
        $stmt->bindValue(':lear', $vocabularyItem->IsLearned, PDO::PARAM_BOOL);
        $stmt->bindValue(':cor', $vocabularyItem->CorrectAnswers, PDO::PARAM_INT);
        $stmt->bindValue(':up', $vocabularyItem->databaseFormattedUpdatedAt(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $vocabularyItem->Id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * @return VocabularyItem[]
     */
    private function getVocabulary(int $userId, int $languageId, string $query, int $limit): array
    {
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lang', $languageId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS, VocabularyItem::class);

        if (empty($result)) {
            return [];
        }

        return $result;
    }
}
