<?php

namespace services\user;

use DateTime;
use Exception;
use mapping\UserMapper;
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

        $passwordHash = 'a';

        if ($user !== null) {
            $passwordHash = $user->PasswordHash ?? 'a';
        }

        if ($this->authService->isUserPasswordValid($passwordHash, $request->password) === false) {
            throw new ApplicationException("Invalid user's e-mail or password", 400);
        }

        if ($user === null) {
            throw new ApplicationException("Invalid user's e-mail or password", 400);
        }

        if ($user->VerificationKey !== null) {
            throw new ApplicationException("Invalid user's credentials", 400);
        }

        if ($user->IsVerified === false) {
            throw new ApplicationException("User's e-mail address is not verified yet", 403);
        }

        $tokenResponse = $this->authService->generateAuthTokens($user);

        return UserMapper::mapToAuthResponse($user, $tokenResponse);
    }

    public function logout(LogoutRequest $request): void
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user->Id !== $request->userId) {
            throw new ApplicationException("Not allowed to log out user with provided ID", 403);
        }

        $this->authService->invalidateRefreshToken($user->Id, $request->refreshToken);
    }


    public function register(RegisterRequest $request): RegisterResponse
    {
        $existingUser = $this->userRepository->getByEmail($request->email);

        if ($existingUser !== null) {
            throw new ApplicationException("User with provided e-mail is already registered.", 400);
        }

        $passwordHash = $this->authService->hashPassword($request->password);

        $user = UserMapper::mapRegisterRequestToUser($request, $passwordHash);

        $user->VerificationKey = $this->authService->createVerificationKey($user->Email);

        $user = $this->userRepository->create($user);

        if ($user === null) {
            throw new ApplicationException("Failed to create user", 500);
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

        $response = new RegisterResponse();
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
        $response = $this->authService->refreshTokens($request->refreshToken);

        if (!$response) {
            throw new ApplicationException("Not able to authorize user", 401);
        }

        return $response;
    }

    public function changePassword(ChangePasswordRequest $request): AuthResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user === null) {
            throw new ApplicationException("User was not found", 404);
        }

        if ($this->authService->isUserPasswordValid($user->PasswordHash, $request->previousPassword) === false) {
            throw new ApplicationException("Invalid user's password", 400);
        }

        $tokenResponse = $this->authService->changePassword($user, $request->newPassword);

        return UserMapper::mapToAuthResponse($user, $tokenResponse);
    }

    public function forgetPassword(ForgotPasswordRequest $request): void
    {
        $user = $this->userRepository->getByEmail($request->email);

        if ($user === null) {
            throw new ApplicationException("User with provided e-mail was not found", 404);
        }

        if ($user->IsVerified === false) {
            throw new ApplicationException("User's e-mail address is not verified yet", 403);
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
