<?php

namespace services\user;

use DateTime;
use Exception;
use mapping\UserMapper;
use models\domain\user\AuthToken;
use models\domain\user\User;
use models\email\EmailMessage;
use models\RegistrationConfiguration;
use models\request\ChangePasswordRequest;
use models\request\LoginRequest;
use models\request\LogoutRequest;
use models\request\RefreshTokensRequest;
use models\request\RegisterRequest;
use models\response\AuthResponse;
use models\response\RegisterResponse;
use models\response\RegisterResponseMessage;
use models\response\TokenResponse;
use models\response\UserResponse;
use repository\user\UserRepository;
use services\user\auth\AuthService;
use WebApiCore\Exceptions\ApplicationException;

class UserService
{
    public function __construct(
        private readonly RegistrationConfiguration $registrationConfiguration,
        private readonly AuthService $authService,
        private readonly UserRepository $userRepository,
        private readonly EmailSenderService $emailSender
    ) {}

    public function getAuthenticatedUser() : UserResponse {
        $user = $this->authService->getAuthenticatedUser();

        if ($user === null) {
            throw new ApplicationException("User is not authenticated", 401);
        }

        return UserMapper::mapToUserResponse($user);
    }

    /**
     * @return \models\response\AuthResponse|null returns response object if user's credentials are valid otherwise returns `null`
     */
    public function login(LoginRequest $request): ?AuthResponse
    {
        $authUserId = $this->authService->getAuthenticatedUserId();

        if ($authUserId !== null) {
            throw new ApplicationException("User is already logged in.", 400);
        }

        $user = $this->userRepository->getByEmail($request->email);

        if ($user === null) {
            throw new ApplicationException("Invalid user's e-mail or password.", 422);
        }

        if ($user->IsVerified === false) {
            throw new ApplicationException("User's e-mail address is not verified yet", 403);
        }

        $user = $this->authService->login($user, $request->password);

        if ($user === null) {
            throw new ApplicationException("Invalid user's e-mail or password.", 422);
        }

        return UserMapper::mapToAuthResponse($user);
    }

    public function logout(LogoutRequest $request): void
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user->Id !== $request->userId) {
            throw new ApplicationException("Not allowed to log out user with provided ID", 403);
        }

        $user->RefreshToken = $request->refreshToken;

        $this->authService->logout($user);
    }


    public function register(RegisterRequest $request): RegisterResponse
    {
        $existingUser = $this->userRepository->getByEmail($request->email);

        if ($existingUser !== null) {
            throw new ApplicationException("User with provided e-mail is already registered.", 422);
        }

        $user = UserMapper::mapRegisterRequestToUser($request);

        if ($this->registrationConfiguration->IsEmailVerificationRequired) {
            $user->IsVerified = false;
            $user->VerificationKey = $this->authService->createVerificationKey($user->Email);
        } else {
            $user->IsVerified = true;
        }

        $user = $this->authService->registerValidUser($user, $request->password);

        $response = new RegisterResponse();

        if (!$this->registrationConfiguration->IsEmailVerificationRequired && $user->IsVerified) {
            $response->auth = UserMapper::mapToAuthResponse($user);
        } else {
            $this->sendVerificationEmail($user);
            $response->registration = new RegisterResponseMessage();
            $response->registration->userEmail = $user->Email;
            $response->registration->message = "User was successfully registered. Please verify your e-mail address.";
        }

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

        $this->sendVerifiedEmail($user);
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

    public function changePassword(ChangePasswordRequest $request): void
    {
        $user = $this->userRepository->getByEmail($request->userEmail);

        if ($user === null) {
            throw new ApplicationException("User with provided e-mail was not found", 404);
        }

        if ($user->VerificationKey !== trim($request->verificationKey)) {
            throw new ApplicationException("Invalid security key", 422);
        }
    }


    private function sendVerificationEmail(User $user, string $recipientName = ''): void
    {
        $verificationLink = $this->registrationConfiguration->VerificationClientLink . $user->VerificationKey;

        $message = new EmailMessage();
        $message->subject = "E-mail verification";
        $message->body =
            "<h2 style=\"margin: 25px auto 35px 15px;display: flex;flex-direction: column;row-gap: 10px;\">
        <span>Hello from Word App&nbsp;</span>
        <span style=\"font-size: 0.9em\">Vocabulary learning</span>
        </h2>
        <h3>Thank you for your registration on our web site.</h3>
        <p>Please, verify your e-mail addres to fully enjoy our web application.</p>        
        <p>To verify your e-mail address please use this link by clicking on it: <a style=\"color: #1961b6; font-weight: 600; font-size: 1.2em\" href=\"{$verificationLink}\">Verification</a>.<br /></p>
        <p style=\"font-size: 0.9em\">If this request wasn't made by you, please disregard or delete this email.</p>";
        $message->plainMessage = "Hello from Word App\n 
        Thank you for your registration on our web site.\n
        Please, verify your e-mail addres to fully enjoy our web about fashion.
        To verify your e-mail address please use this link: {$verificationLink}\n
        If this request wasn't made by you, please ignore or delete this email.";
        $message->recipientAddress = $user->Email;
        $message->recipientName = $recipientName;

        $this->emailSender->sendMail($message);
    }

    private function sendVerifiedEmail(User $user, string $recipientName = ''): void
    {
        $message = new EmailMessage();
        $message->subject = "E-mail verified";
        $message->body =
            "<h2 style=\"margin: 25px auto 35px 15px;display: flex;flex-direction: column;row-gap: 10px;\">
        <span>Hello from Word App&nbsp;</span>
        <span style=\"font-size: 0.9em\">Vocabulary learning</span>
        </h2>
        <h3>Thank you for your registration on our web site.</h3>
        <p>Your e-mail address was successfully verified.</p>
        <p>We hope you will like our web application for vocabulary learning.<br /></p>        
        <p style=\"font-size: 0.9em\">If this email doesn't belong to you, please ignore or delete it.</p>";
        $message->plainMessage = "Hello from Feelofalai Fashion Blog\n 
        Thank you for your registration/subscription on our web site.\n
        Your e-mail address was successfully verified.
        We hope you will like our web application for vocabulary learning.\n\n
        If this email doesn't belong to you, please ignore or delete it.";
        $message->recipientAddress = $user->Email;
        $message->recipientName = $recipientName;

        $this->emailSender->sendMail($message);
    }
}
