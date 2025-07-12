<?php

$otherSettingsPath = __DIR__ . '/.otherSettings.php';

$confData = [
    "auth" => [
        "senderType" => "email",
        "verificationLink" => "http://localhost:3001/word-app/signup/verification",
        "loginLink" => "http://localhost:3001/word-app/login",
        "resetLink" => "http://localhost:3001/word-app/reset-password",
        "useNoReply" => true
    ]
];

if (file_exists($otherSettingsPath)) {
    require_once($otherSettingsPath);
    $confData = array_merge($confData, $otherSettingsData);
}
