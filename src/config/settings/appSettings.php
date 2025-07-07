<?php

$otherSettingsPath = __DIR__ . '/.otherSettings.php';

$confData = [
    "emailService" => [
        "verificationLink" => "http://localhost:3001/word-app/signup/verification/",
        "loginLink" => "http://localhost:3001/word-app/login",
        "resetPasswordLink" => "http://localhost:3001/word-app/reset-password/",
    ]
];

if (file_exists($otherSettingsPath)) {
    require_once($otherSettingsPath);
    $confData = array_merge($confData, $otherSettingsData);
}
