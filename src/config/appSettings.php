<?php

$otherSettingsPath = __DIR__ . '/otherSettings.php';

$confData = [
    "EMAIL_SERVER" => "smtp.webzdarma.cz",
    "EMAIL_SENDER_ADR" => "info@hyl-petr.xf.cz",
    "EMAIL_SENDER_NM" => "Feelofalai",
    "EMAIL_PORT" => "587",
    "VERIFICATION_LINK" => "https://localhost:3000/blog/user/verification/",
    "EMAIL_VERIFICATION" => true,    
];

if (file_exists($otherSettingsPath)) {
    require_once($otherSettingsPath);
    $confData = array_merge($confData, $otherSettingsData);
}
