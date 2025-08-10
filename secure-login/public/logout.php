<?php
require_once __DIR__ . '/../src/bootstrap.php';
$authConfig = require __DIR__ . '/../config/auth.php';
$cookieName = $authConfig['session_cookie'] ?? 'session_token';

if (isset($_COOKIE[$cookieName])) {
    $token = $_COOKIE[$cookieName];
    if (!empty($token)) {
        $redisClient->deleteSession($token);
    }
    setcookie($cookieName, '', time() - 3600, '/');
    unset($_COOKIE[$cookieName]);
}

header('Location: login.php');
exit;

