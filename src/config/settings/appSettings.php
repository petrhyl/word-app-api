<?php

$otherSettingsPath = __DIR__ . '/.otherSettings.php';

$confData = [
    "EMAIL_SERVER" => "smtp.webzdarma.cz",
    "EMAIL_SENDER_ADR" => "info@hyl-petr.xf.cz",
    "EMAIL_SENDER_NM" => "Word App",
    "EMAIL_PORT" => "587",
    "VERIFICATION_LINK" => "http://localhost:3001/word-app/signup/verification/",
    "LOGIN_LINK" => "http://localhost:3001/word-app/login",
    "EMAIL_VERIFICATION" => true,
    "RESET_PASSWORD_LINK" => "http://localhost:3001/word-app/reset-password/",
];

if (file_exists($otherSettingsPath)) {
    require_once($otherSettingsPath);
    $confData = array_merge($confData, $otherSettingsData);
}
