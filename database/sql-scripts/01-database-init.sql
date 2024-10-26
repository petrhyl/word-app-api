DROP TABLE IF EXISTS Wordapp_Users;
CREATE TABLE Wordapp_Users(
    Id INT NOT NULL AUTO_INCREMENT,
    Email VARCHAR(150) NOT NULL,
    Name VARCHAR(150) NOT NULL,
    PasswordHash VARCHAR(260) NOT NULL,
    VerificationKey CHAR(64),
    IsVerified BOOLEAN NOT NULL DEFAULT FALSE,
    Language CHAR(2) NOT NULL,
    UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (Id),
    UNIQUE (Email),
    UNIQUE (VerificationKey)
);
DROP TABLE IF EXISTS Wordapp_UserLogins;
CREATE TABLE Wordapp_UserLogins(
    Id INT NOT NULL AUTO_INCREMENT,
    UserId INT NOT NULL,
    TokenHash VARCHAR(260) NOT NULL,
    ExpiresIn DATETIME NOT NULL,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (Id),
    UNIQUE (TokenHash, UserId)
);
DROP TABLE IF EXISTS Wordapp_VocabularyLanguages;
CREATE TABLE Wordapp_VocabularyLanguages(
    Id INT NOT NULL AUTO_INCREMENT,
    UserId INT NOT NULL,
    Code CHAR(2) NOT NULL,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (Id),
    UNIQUE (UserId, Code)
);
DROP TABLE IF EXISTS Wordapp_Vocabularies;
CREATE TABLE Wordapp_Vocabularies(
    Id INT NOT NULL AUTO_INCREMENT,
    UserId INT NOT NULL,
    VocabularyLanguageId INT NOT NULL,
    Value VARCHAR(150) NOT NULL,
    Translations VARCHAR(300) NOT NULL,
    IsLearned BOOLEAN NOT NULL DEFAULT FALSE,
    CorrectAnswers BIGINT NOT NULL DEFAULT 0,
    UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (Id),
    UNIQUE (UserId, Value, VocabularyLanguageId),
    INDEX idx_trans_user_id (UserId, VocabularyLanguageId, IsLearned),
    INDEX idx_user_id_language_id (UserId, VocabularyLanguageId)
);
DROP TABLE IF EXISTS Wordapp_ExerciseResults;
CREATE TABLE Wordapp_ExerciseResults(
    Id INT NOT NULL AUTO_INCREMENT,
    UserId INT NOT NULL,
    VocabularyLanguageId INT NOT NULL,
    CorrectAnswers BIGINT NOT NULL,
    IncorrectAnswers BIGINT NOT NULL,
    CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (Id),
    INDEX idx_user_id_language (UserId, VocabularyLanguageId)
);

ALTER TABLE Wordapp_UserLogins
    ADD FOREIGN KEY (UserId) REFERENCES Wordapp_Users(Id) ON DELETE CASCADE;
ALTER TABLE Wordapp_VocabularyLanguages
    ADD FOREIGN KEY (UserId) REFERENCES Wordapp_Users(Id) ON DELETE CASCADE;
ALTER TABLE Wordapp_Vocabularies
    ADD FOREIGN KEY (UserId) REFERENCES Wordapp_Users(Id) ON DELETE CASCADE,
    ADD FOREIGN KEY (VocabularyLanguageId) REFERENCES Wordapp_VocabularyLanguages(Id) ON DELETE CASCADE;
ALTER TABLE Wordapp_ExerciseResults
    ADD FOREIGN KEY (UserId) REFERENCES Wordapp_Users(Id) ON DELETE CASCADE,
    ADD FOREIGN KEY (VocabularyLanguageId) REFERENCES Wordapp_VocabularyLanguages(Id) ON DELETE CASCADE;