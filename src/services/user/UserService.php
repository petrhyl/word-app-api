<?php

namespace services\user;

use DateTime;
use Exception;
use mapping\UserMapper;
use models\domain\user\AuthToken;
use models\domain\user\User;
use services\user\auth\configuration\AuthConfiguration;
use models\request\ChangePasswordRequest;
use models\request\ForgotPasswordRequest;
use models\request\LoginRequest;
use models\request\LogoutRequest;
use models\request\ResetPasswordRequest;
use models\request\RefreshTokensRequest;
use models\request\RegisterRequest;
use models\request\SendEmailRequest;
use models\request\UpdateUserRequest;
use models\response\AuthResponse;
use models\response\RegisterResponse;
use models\response\RegisterResponseMessage;
use models\response\TokenResponse;
use models\response\UserResponse;
use repository\user\UserRepository;
use services\message\message\recipient\MessageRecipient;
use services\message\message\template\MessageTemplateType;
use services\message\UserMessageService;
use services\user\auth\AuthService;
use WebApiCore\Exceptions\ApplicationException;

class UserService
{
    public function __construct(
        private readonly AuthConfiguration $authConfiguration,
        private readonly AuthService $authService,
        private readonly UserRepository $userRepository,
        private readonly UserMessageService $messageService
    ) {}

    public function getAuthenticatedUser(): UserResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user === null) {
            throw new ApplicationException("User is not authenticated", 401);
        }

        return UserMapper::mapToUserResponse($user);
    }

    /**
     * @return \models\response\AuthResponse returns response object if user's credentials are valid
     */
    public function login(LoginRequest $request): AuthResponse
    {
        $authUserId = $this->authService->getAuthenticatedUserId();

        if ($authUserId !== null) {
            throw new ApplicationException("User is already logged in", 400);
        }

        $user = $this->userRepository->getByEmail($request->email);

        if ($user === null) {
            throw new ApplicationException("Invalid user's e-mail or password", 400);
        }

        if ($user->VerificationKey !== null) {
            throw new ApplicationException("Invalid user's credentials", 400);
        }

        if ($user->IsVerified === false) {
            throw new ApplicationException("User's e-mail address is not verified yet", 403);
        }

        $user = $this->authService->login($user, $request->password);

        if ($user === null) {
            throw new ApplicationException("Invalid user's e-mail or password", 400);
        }

        return UserMapper::mapToAuthResponse($user);
    }

    public function logout(LogoutRequest $request): void
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user->Id !== $request->userId) {
            throw new ApplicationException("Not allowed to log out user with provided ID", 403);
        }

        $user->RefreshToken = new AuthToken();
        $user->RefreshToken->Value = $request->refreshToken;

        $this->authService->logout($user);
    }


    public function register(RegisterRequest $request): RegisterResponse
    {
        $existingUser = $this->userRepository->getByEmail($request->email);

        if ($existingUser !== null) {
            throw new ApplicationException("User with provided e-mail is already registered.", 400);
        }

        $user = UserMapper::mapRegisterRequestToUser($request);

        $user->IsVerified = false;
        $user->VerificationKey = $this->authService->createVerificationKey($user->Email);

        $user = $this->authService->registerValidUser($user, $request->password);

        $response = new RegisterResponse();

        $this->messageService->send(
            new MessageRecipient($user->Email, $user->Name),
            $this->authConfiguration->getMessageSender(),
            MessageTemplateType::RegistrationVerification,
            [
                "verificationLink" => $this->authConfiguration->verificationLink,
                "verificationKey" => $user->VerificationKey
            ]
        );

        $response->registration = new RegisterResponseMessage();
        $response->registration->userEmail = $user->Email;
        $response->registration->message = "User was successfully registered. Please verify your e-mail address.";

        return $response;
    }

    public function verify(string $verificationKey): void
    {
        $user = $this->userRepository->getByVerificationKey($verificationKey);

        if ($user === null) {
            throw new ApplicationException("Invalid verification key.", 400);
        }

        $user->IsVerified = true;
        $user->UpdatedAt = new DateTime();
        $user->VerificationKey = null;
        $result = $this->userRepository->update($user);

        if (!$result) {
            throw new Exception("Failed to update EmailAddress in database during verification", 101);
        }

        $this->messageService->send(
            new MessageRecipient($user->Email, $user->Name),
            $this->authConfiguration->getMessageSender(),
            MessageTemplateType::ConfirmedVerification,
            [
                "loginLink" => $this->authConfiguration->loginLink
            ]
        );
    }

    public function refreshTokens(RefreshTokensRequest $request): TokenResponse
    {
        $userId = $this->authService->getClaimFromToken(AuthService::USER_ID_CLAIM, $request->accessToken);

        if ($userId !== null) {
            throw new ApplicationException("User is already authorized", 400);
        }

        $areTokensValid = $this->authService->areTokensValid($request->refreshToken, $request->accessToken);

        if (!$areTokensValid) {
            throw new ApplicationException("Not able to authorize user", 401);
        }

        $userId = filter_var($this->authService->getClaimFromToken(AuthService::USER_ID_CLAIM, $request->refreshToken), FILTER_VALIDATE_INT);

        if ($userId === false) {
            throw new ApplicationException("Not able to authorize user", 401);
        }

        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new ApplicationException("Not able to authorize user", 401);
        }

        if ($user->VerificationKey !== null) {
            throw new ApplicationException("Invalid user's credentials", 403);
        }

        $user->AccessToken = new AuthToken();
        $user->AccessToken->Value = $request->accessToken;

        $user->RefreshToken = new AuthToken();
        $user->RefreshToken->Value = $request->refreshToken;

        $user = $this->authService->refreshTokens($user);

        if ($user === null) {
            throw new ApplicationException("Not able to authorize user", 401);
        }

        return UserMapper::mapToTokenResponse($user);
    }

    public function changePassword(ChangePasswordRequest $request): AuthResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user === null) {
            throw new ApplicationException("User was not found", 404);
        }

        if ($this->authService->isUserPasswordValid($user, $request->previousPassword) === false) {
            throw new ApplicationException("Invalid user's password", 400);
        }

        $user = $this->authService->changePassword($user, $request->newPassword);

        return UserMapper::mapToAuthResponse($user);
    }

    public function forgetPassword(ForgotPasswordRequest $request): void
    {
        $user = $this->userRepository->getByEmail($request->email);

        if ($user === null) {
            throw new ApplicationException("User with provided e-mail was not found", 404);
        }

        $user->VerificationKey = $this->authService->createVerificationKey($user->Email);
        $user->UpdatedAt = new DateTime();

        $result = $this->userRepository->update($user);

        if (!$result) {
            throw new Exception("Failed to update EmailAddress in database during verification", 101);
        }

        $this->messageService->send(
            new MessageRecipient($user->Email, $user->Name),
            $this->authConfiguration->getMessageSender(),
            MessageTemplateType::ForgottenPassword,
            [
                "resetLink" => $this->authConfiguration->resetLink,
                "resetKey" => $user->VerificationKey
            ]
        );
    }

    public function resetPassword(ResetPasswordRequest $request): void
    {
        $user = $this->userRepository->getByVerificationKey($request->verificationKey);

        if ($user === null) {
            throw new ApplicationException("Invalid verification key", 400);
        }

        $user = $this->authService->createNewPassword($user, $request->password);
    }

    public function updateUserData(UpdateUserRequest $request): UserResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user === null) {
            throw new ApplicationException("User was not found", 404);
        }

        $user->Name = $request->name;
        $user->UpdatedAt = new DateTime();

        $result = $this->userRepository->update($user);

        if (!$result) {
            throw new Exception("Failed to update user's data in database", 101);
        }

        return UserMapper::mapToUserResponse($user);
    }

    public function sendEmailToVerify(SendEmailRequest $request): void
    {
        $user = $this->userRepository->getByEmail($request->email);

        if ($user === null) {
            throw new ApplicationException("User with provided e-mail was not found", 404);
        }

        if ($user->IsVerified) {
            throw new ApplicationException("User's e-mail address is already verified", 400);
        }

        $user->UpdatedAt = new DateTime();
        $user->VerificationKey = $this->authService->createVerificationKey($user->Email);
        $result = $this->userRepository->update($user);

        if (!$result) {
            throw new Exception("Failed to update EmailAddress in database during verification", 101);
        }

        $this->messageService->send(
            new MessageRecipient($user->Email, $user->Name),
            $this->authConfiguration->getMessageSender(),
            MessageTemplateType::RegistrationVerification,
            [
                "verificationLink" => $this->authConfiguration->verificationLink,
                "verificationKey" => $user->VerificationKey
            ]
        );
    }
}
