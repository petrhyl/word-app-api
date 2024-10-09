<?php

namespace repository\vocabulary;

use config\Database;
use DateTime;
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
    public function getUserUnlearnedVocabulary(int $userId, string $language, int $limit): array
    {
        $query = self::GET_WORD_QUERY .
            " WHERE UserId = :usid AND Language = :lang AND IsLearned = 0" .
            self::ORDER_BY_QUERY .
            " LIMIT :lim";

        return $this->getUserVocabulary($userId, $language, $query, $limit);
    }

    /**
     * @return VocabularyItem[]
     */
    public function getUserLearnedVocabulary(int $userId, string $language, int $limit): array
    {
        $query = self::GET_WORD_QUERY .
            " WHERE UserId = :usid AND Language = :lang AND IsLearned = 1" .
            self::ORDER_BY_QUERY
            . " LIMIT :lim";

        return $this->getUserVocabulary($userId, $language, $query, $limit);
    }

    /**
     * @return VocabularyItem[]
     */
    private function getUserVocabulary(int $userId, string $language, string $query, int $limit): array
    {
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':usid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lang', $language, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return [];
        }

        $vocabulary = [];
        foreach ($result as $item) {
            $vocabulary[] = $this->convertAssocArrayToVocabulary($item);
        }

        return $vocabulary;
    }

    /**
     * @param VocabularyItem[] $vocabularyItems
     */
    public function createVocabulary(array $vocabularyItems): bool
    {
        $command = "INSERT INTO Wordapp_Vocabularies (UserId, Value, Translations, Language) 
        VALUES ";

        foreach ($vocabularyItems as $key => $value) {
            $command .= "(:usid{$key}, :val{$key}, :trans{$key}, :lang{$key}),\n";
        }

        $command = rtrim($command, ",\n");

        $stmt = $this->conn->prepare($command);

        foreach ($vocabularyItems as $key => $value) {
            $stmt->bindValue(":usid{$key}", $value->UserId, PDO::PARAM_INT);
            $stmt->bindValue(":val{$key}", $value->Value, PDO::PARAM_STR);
            $stmt->bindValue(":trans{$key}", $value->Translations, PDO::PARAM_STR);
            $stmt->bindValue(":lang{$key}", $value->Language, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    public function createVocabularyItem(VocabularyItem $vocabularyItem): bool
    {
        $command = "INSERT INTO Wordapp_Vocabularies (UserId, Value, Translations, Language)
        VALUES (:usid, :val, :trans, :lang)";

        $stmt = $this->conn->prepare($command);

        $stmt->bindValue(':usid', $vocabularyItem->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':val', $vocabularyItem->Value, PDO::PARAM_STR);
        $stmt->bindValue(':trans', $vocabularyItem->Translations, PDO::PARAM_STR);
        $stmt->bindValue(':lang', $vocabularyItem->Language, PDO::PARAM_STR);

        return $stmt->execute();
    }


    private function convertAssocArrayToVocabulary(array $data): VocabularyItem
    {
        $vocabulary = new VocabularyItem();
        $vocabulary->Id = $data['Id'];
        $vocabulary->UserId = $data['UserId'];
        $vocabulary->Value = $data['Value'];
        $vocabulary->Translations = $data['Translations'];
        $vocabulary->Language = $data['Language'];
        $vocabulary->IsLearned = $data['IsLearned'];
        $vocabulary->CorrectAnswers = $data['CorrectAnswers'];
        $vocabulary->CreatedAt = new DateTime($data['CreatedAt']);
        $vocabulary->UpdatedAt = new DateTime($data['UpdatedAt']);

        return $vocabulary;
    }
}
