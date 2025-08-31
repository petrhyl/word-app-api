<?php

namespace services\user\auth;

use config\ErrorHandler;
use DateTime;
use DateTimeZone;
use Exception;
use models\response\AuthToken;
use models\domain\user\User;
use models\domain\user\UserLogin;
use models\response\TokenResponse;
use repository\user\UserRepository;
use repository\user\TokenRepository;
use services\EncryptionService;
use WebApiCore\Builder\App;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenService $tokenService,
        private readonly TokenRepository $tokenRepository,
        private readonly EncryptionService $encryption
    ) {}

    public const USER_ID_CLAIM = 'sub';
    public const NAME_CLAIM = 'name';
    public const EMAIL_CLAIM = 'email';
    public const ADMIN_CLAIM = 'admin';
    public const EXPIRE_IN_CLAIM = 'exp';


    public function getAuthenticatedUser(): ?User
    {
        if (App::$request->user === null) {
            return null;
        }

        return $this->userRepository->getById(App::$request->user->id);
    }

    public function getAuthenticatedUserId(): ?int
    {
        if (App::$request->user === null) {
            return null;
        }

        return App::$request->user->id;
    }

    public function getValidTokenEntity(string $refreshToken): ?UserLogin
    {
        $tokenValueAndBase = $this->unwireRefreshToken($refreshToken);

        if ($tokenValueAndBase === null) {
            return null;
        }

        $refreshTokenEntity = $this->tokenRepository->getById($tokenValueAndBase[0]);

        if ($refreshTokenEntity === null) {
            return null;
        }

        $refreshTokenHash = $this->encryption->hashWithSecret($tokenValueAndBase[1], false);

        if ($refreshTokenHash !== $refreshTokenEntity->TokenHash) {
            return null;
        }

        return $refreshTokenEntity;
    }

    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @return \models\response\TokenResponse
     */
    public function generateAuthTokens(User $user): TokenResponse
    {
        $accessToken = $this->createAccessToken($user);
        $refreshToken = $this->createRefreshToken($user);

        return new TokenResponse($accessToken, $refreshToken);
    }

    /**
     * @return \models\response\TokenResponse|null returns `null` if refresh token is not stored in database with user id
     * @throws \Exception if failed to delete refresh token from database
     */
    public function refreshTokens(string $refreshToken): ?TokenResponse
    {
        $tokenEntity = $this->getValidTokenEntity($refreshToken);

        if ($tokenEntity === null) {
            return null;
        }

        $user = $this->userRepository->getById($tokenEntity->UserId);

        if ($user === null || $user->VerificationKey !== null) {
            return null;
        }

        $result = $this->tokenRepository->delete($tokenEntity->Id, $user->Id);

        if ($result === false) {
            throw new Exception("Failed to delete refresh token [ {$tokenEntity->Id} ] from database", 101);
        }

        return $this->generateAuthTokens($user);
    }

    /**
     * @return \models\response\TokenResponse newly created auth tokens
     * @throws \Exception if failed to update user or delete user's tokens
     */
    public function changePassword(User $user, $newPassword): TokenResponse
    {
        $user = $this->createNewPassword($user, $newPassword);

        return $this->generateAuthTokens($user);
    }

    /**
     * @throws \Exception if failed to update user or delete user's tokens
     */
    public function createNewPassword(User $user, $newPassword): User
    {
        $user->PasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->VerificationKey = null;
        $user->UpdatedAt = new DateTime();

        $result = $this->userRepository->update($user);

        if ($result === false) {
            throw new Exception("Failed to update user", 101);
        }

        $result = $this->tokenRepository->deleteAllUserTokens($user->Id);

        if ($result === false) {
            throw new Exception("Failed to delete user's tokens", 101);;
        }

        return $user;
    }


    public function getClaimFromToken(string $claimKey, string $token): mixed
    {
        $claims = $this->getUserClaimsFromToken($token);

        if (empty($claims)) {
            return null;
        }

        return $claims[$claimKey];
    }

    public function getUserClaimsFromToken($token): ?array
    {
        $claims = $this->tokenService->decodeDataFromToken($token);

        if ($claims === null) {
            return null;
        }

        if (($claims[self::EXPIRE_IN_CLAIM] - 5) < time()) {
            return null;
        }

        return $claims;
    }

    public function createVerificationKey(string $userEmail): string
    {
        $now = new DateTime();
        $additionalRandomString = bin2hex(random_bytes(8));
        return $this->encryption->hashWithSecret($userEmail . $additionalRandomString . $now->format('c'), false);
    }

    public function isUserPasswordValid(string $passwordHash, string $password): bool
    {
        return password_verify($password, $passwordHash);
    }

    /**
     * @return bool returns `false` if refresh token is not found or if failed to delete otherwise returns `true`
     */
    public function invalidateRefreshToken(int $userId, string $refreshToken): bool
    {
        $tokenEntity = $this->getValidTokenEntity($refreshToken);

        if ($tokenEntity === null) {
            $ex = new Exception("Logging-out refresh token [ {$refreshToken} ] of user ID [ {$userId} ] was not found in database", 101);
            ErrorHandler::logErrors([ErrorHandler::formatExceptionToLog($ex)]);

            return false;
        }

        if ($tokenEntity->UserId !== $userId) {
            $ex = new Exception("Logging-out refresh token ID [ {$tokenEntity->Id} ] of user ID [ {$tokenEntity->UserId} ] does not match authenticated user ID [ {$userId} ]", 101);
            ErrorHandler::logErrors([ErrorHandler::formatExceptionToLog($ex)]);

            return false;
        }

        $result = $this->tokenRepository->delete($tokenEntity->Id);

        if (!$result) {
            $ex = new Exception("Failed to delete refresh token [ {$tokenEntity->Id} ] from database", 101);
            ErrorHandler::logErrors([ErrorHandler::formatExceptionToLog($ex)]);

            return false;
        }

        return true;
    }

    private function createAccessToken(User $user): AuthToken
    {
        $currentTimestampInSeconds = $this->getCurrentTimestampInSeconds();
        $secondsToExpire = $currentTimestampInSeconds + 1200;
        $expiryTimestamp = $secondsToExpire * 1000;

        $claims = [
            self::USER_ID_CLAIM => $user->Id,
            self::EMAIL_CLAIM => $user->Email,
            self::EXPIRE_IN_CLAIM => $secondsToExpire
        ];

        $token = $this->tokenService->generateJwtToken($claims);

        return new AuthToken($token, $expiryTimestamp);
    }

    private function createRefreshToken(User $user): AuthToken
    {
        $refreshTokenBase = $this->tokenService->generateRefreshToken();

        $hashedToken = $this->encryption->hashWithSecret($refreshTokenBase, false);

        $currentTimestampInSeconds = $this->getCurrentTimestampInSeconds();
        $secondsToExpire = $currentTimestampInSeconds + 15_552_000;

        $entity = new UserLogin();
        $entity->UserId = $user->Id;
        $entity->TokenHash = $hashedToken;
        $entity->setExpiresIn(new DateTime("@$secondsToExpire"));

        $entity = $this->tokenRepository->store($entity);

        if ($entity === null) {
            throw new Exception("Failed to store refresh token in database.");
        }

        $refreshToken = $this->wireRefreshToken($entity->Id, $refreshTokenBase);

        return new AuthToken($refreshToken, $secondsToExpire * 1000);
    }

    private function getCurrentTimestampInSeconds(): int
    {
        return (new DateTime())->setTimezone(new DateTimeZone('UTC'))->getTimestamp();
    }

    private function wireRefreshToken(int $tokenId, string $refreshTokenBase): string
    {
        return "{$tokenId}.{$refreshTokenBase}";
    }

    /**
     * @return array{0: int, 1: string}|null - Token ID as first item and base as second one of the array
     */
    private function unwireRefreshToken(string $wiredToken): array|null
    {
        $tokenIdAndBase = explode('.', $wiredToken);

        if (count($tokenIdAndBase) < 2) {
            return null;
        }

        [$tokenId, $refreshTokenBase] = $tokenIdAndBase;

        if (empty($refreshTokenBase)) {
            return null;
        }

        if (filter_var($tokenId, FILTER_VALIDATE_INT) === false) {
            return null;
        }

        return [(int)$tokenId, $refreshTokenBase];
    }
}
