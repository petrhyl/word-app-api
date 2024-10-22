<?php

namespace repository\exercise;

use config\Database;
use DateTime;
use Exception;
use models\domain\exercise\ExerciseResult;
use models\domain\exercise\LanguageExerciseResult;
use repository\vocabulary\VocabularyRepository;
use PDO;
use WebApiCore\Exceptions\ApplicationException;

class ExerciseRepository
{
    private readonly PDO $conn;

    public function __construct(
        Database $database,
        private VocabularyRepository $vocabularyRepository
    ) {
        $this->conn = $database->getConnection();
    }

    private const GET_EXERCISE_RESULT_QUERY =
    "SELECT UserId, VocabularyLanguageId, vl.Code AS VocabularyLanguageCode, SUM(CorrectAnswers) AS CorrectAnswers, SUM(IncorrectAnswers) AS IncorrectAnswers, COUNT(Id) AS ExercisesCount
         FROM Wordapp_ExerciseResults 
         INNER JOIN Wordapp_VocabularyLanguages AS vl ON Wordapp_ExerciseResults.VocabularyLanguageId = vl.Id ";

    public function create(ExerciseResult $exercise): bool
    {
        $command = "INSERT INTO Wordapp_ExerciseResults (UserId, VocabularyLanguageId, CorrectAnswers, IncorrectAnswers)
        VALUES (:userId, :vocabularyLanguageId, :correctAnswers, :incorrectAnswers)";

        $stmt = $this->conn->prepare($command);

        $stmt->bindValue(':userId', $exercise->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':vocabularyLanguageId', $exercise->VocabularyLanguageId, PDO::PARAM_INT);
        $stmt->bindValue(':correctAnswers', $exercise->CorrectAnswers, PDO::PARAM_INT);
        $stmt->bindValue(':incorrectAnswers', $exercise->IncorrectAnswers, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @param ExerciseResult $exercise
     * @param \models\request\ExerciseResultRequestItem[] $words
     * @return bool
     */
    public function createResultAndUpdateVocabulary(ExerciseResult $exercise, array $words): bool
    {
        try {
            $this->conn->beginTransaction();

            $result = $this->create($exercise);

            if (!$result) {
                throw new Exception("Failed to create exercise result", 101);
            }

            foreach ($words as $word) {
                $vocabularyItem = $this->vocabularyRepository->getVocabularyItem($word->id);

                if (!$vocabularyItem) {
                    throw new ApplicationException("Vocabulary item not found", 404);
                }

                if ($vocabularyItem->UserId !== $exercise->UserId) {
                    throw new ApplicationException("Vocabulary item not found", 404);
                }

                if ($vocabularyItem->VocabularyLanguageId !== $exercise->VocabularyLanguageId) {
                    throw new ApplicationException("Vocabulary item not found", 404);
                }

                if ($word->isAnswredCorrectly) {
                    $vocabularyItem->CorrectAnswers++;
                }

                if ($word->isAnswredCorrectly !== $vocabularyItem->IsLearned) {
                    $vocabularyItem->IsLearned = $word->isAnswredCorrectly;
                }

                $vocabularyItem->setUpdatedAt(new DateTime());

                $result = $this->vocabularyRepository->updateVocabularyItem($vocabularyItem);
            }

            $this->conn->commit();
        } catch (ApplicationException $e) {
            $this->conn->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            return false;
        }

        return true;
    }

    public function getLanguageExerciseResultOfUser(int $userId, int $languageId): LanguageExerciseResult | null
    {
        $query = self::GET_EXERCISE_RESULT_QUERY . "
        WHERE UserId = :userId AND VocabularyLanguageId = :languageId
        GROUP BY VocabularyLanguageId, VocabularyLanguageCode";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':languageId', $languageId, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchObject(LanguageExerciseResult::class);

        return empty($result) ? null : $result;
    }

    /**
     * @param int $userId
     * @return \models\domain\exercise\LanguageExerciseResult[]
     */
    public function getLanguageExerciseResultsOfUser(int $userId): array
    {
        $query = self::GET_EXERCISE_RESULT_QUERY . "
        WHERE UserId = :userId
        GROUP BY VocabularyLanguageId, VocabularyLanguageCode";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_CLASS, LanguageExerciseResult::class);

        return empty($results) ? [] : $results;
    }
}
