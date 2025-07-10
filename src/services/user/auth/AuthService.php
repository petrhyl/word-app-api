<?php

namespace services\user\auth;

use config\ErrorHandler;
use DateTime;
use DateTimeZone;
use Exception;
use models\domain\user\AuthToken;
use models\domain\user\User;
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

    public function registerValidUser(User $user, string $password): User
    {
        $user->PasswordHash = password_hash($password, PASSWORD_DEFAULT);

        $user = $this->userRepository->create($user);

        if ($user === null) {
            throw new Exception("Failed to create user in database", 101);
        }

        if ($user->IsVerified) {
            return $this->createTokensAndAssignThemToUser($user);
        }

        return $user;
    }

    /**
     * @return \models\domain\user\User|null returns null if password is not valid
     */
    public function login(User $user, string $password): ?User
    {
        if (!$this->isUserPasswordValid($user, $password)) {
            return null;
        }

        return $this->createTokensAndAssignThemToUser($user);
    }

    /**
     * @return bool returns `false` if refresh token is not found or if failed to delete otherwise returns `true`
     */
    public function logout(User $user): bool
    {
        if (empty($user->RefreshToken)) {
            throw new Exception("Missing refresh token of the logging out user", 101);
        }

        $hashedToken = $this->encryption->hashWithSecret($user->RefreshToken->Value, false);

        $exist = $this->tokenRepository->exists($hashedToken, $user->Id);

        if (!$exist) {
            $ex = new Exception("Logging-out user's refresh token [ $hashedToken ] was not found in database", 101);
            ErrorHandler::logErrors([ErrorHandler::formatExceptionToLog($ex)]);

            return false;
        }

        $result = $this->tokenRepository->delete($hashedToken, $user->Id);

        if (!$result) {
            $ex = new Exception("Failed to delete refresh token [ $hashedToken ] from database", 101);
            ErrorHandler::logErrors([ErrorHandler::formatExceptionToLog($ex)]);

            return false;
        }

        return true;
    }

    /**
     * @return \models\domain\user\User|null returns `null` if refresh token is not stored in database with user id
     * @throws \Exception if failed to delete refresh token from database
     */
    public function refreshTokens(User $user): ?User
    {
        $hashedToken = $this->encryption->hashWithSecret($user->RefreshToken->Value, false);

        $result = $this->tokenRepository->exists($hashedToken, $user->Id);

        if ($result === false) {
            return null;
        }

        $result = $this->tokenRepository->delete($hashedToken, $user->Id);

        if ($result === false) {
            throw new Exception("Failed to delete refresh token [ $hashedToken ] from database", 101);
        }

        return $this->createTokensAndAssignThemToUser($user);
    }

    public function areTokensValid(string $refreshToken, string $token): bool
    {
        $claims = $this->tokenService->decodeDataFromToken($token);

        if ($claims === null) {
            return false;
        }

        $userIdFromRefreshToken = $this->getClaimFromToken(self::USER_ID_CLAIM, $refreshToken);

        if ($userIdFromRefreshToken === null) {
            return false;
        }

        $userIdFromToken = filter_var($claims[self::USER_ID_CLAIM], FILTER_VALIDATE_INT);

        if ($userIdFromToken === false) {
            return false;
        }

        if ($userIdFromRefreshToken !== $userIdFromToken) {
            return false;
        }

        return true;
    }

    /**
     * @return \models\domain\user\User user object with auth tokens
     * @throws \Exception if failed to update user or delete user's tokens
     */
    public function changePassword(User $user, $newPassword): User
    {
        $user = $this->createNewPassword($user, $newPassword);

        $user = $this->createTokensAndAssignThemToUser($user);

        return $user;
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

    public function isUserPasswordValid(User $user, string $password): bool
    {
        return password_verify($password, $user->PasswordHash);
    }


    private function createTokensAndAssignThemToUser(User $user): User
    {
        $currentDate = new DateTime();
        $currentDate->setTimezone(new DateTimeZone('UTC'));
        $currentTimestampInSeconds = $currentDate->getTimestamp();


        $secondsToExpire = $currentTimestampInSeconds + 1200;
        $expiryTimestamp = $secondsToExpire * 1000;

        $claims = [
            self::USER_ID_CLAIM => $user->Id,
            self::EMAIL_CLAIM => $user->Email,
            self::EXPIRE_IN_CLAIM => $secondsToExpire
        ];

        $token = $this->tokenService->createToken($claims);

        $accessToken = new AuthToken();
        $accessToken->Value = $token;
        $accessToken->ExpireIn = $expiryTimestamp;

        $secondsToExpire = $currentTimestampInSeconds + 15_552_000;
        $expiryTimestamp = $secondsToExpire * 1000;

        $claims = [
            self::USER_ID_CLAIM => $user->Id,
            self::EXPIRE_IN_CLAIM => $secondsToExpire
        ];

        $refreshToken = $this->tokenService->createToken($claims);

        $hashedToken = $this->encryption->hashWithSecret($refreshToken, false);

        $result = $this->tokenRepository->store(
            $user->Id,
            $hashedToken,
            new DateTime("@$secondsToExpire")
        );

        if ($result === false) {
            throw new Exception("Failed to store refresh token in database.");
        }

        $user->AccessToken = $accessToken;
        $user->RefreshToken = new AuthToken();
        $user->RefreshToken->Value = $refreshToken;
        $user->RefreshToken->ExpireIn = $expiryTimestamp;

        return $user;
    }
}
