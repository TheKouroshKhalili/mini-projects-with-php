<?php

require_once __DIR__ . '/../vendor/autoload.php';

$redisConfig = require __DIR__ . '/../config/redis.php';
$authConfig = require __DIR__ . '/../config/auth.php';

$redisClient = new SecureLogin\Storage\RedisClient($redisConfig['host'], (int)$redisConfig['port']);
$userRepository = new SecureLogin\Storage\UserRepository();
$auth = new SecureLogin\Auth\AuthenticationManager($redisClient, $userRepository, $authConfig);

function is_logged_in() {
    global $redisClient;

    if (!isset($_COOKIE['session_token'])) {
        return false;
    }

    $sessionData = $redisClient->getSession($_COOKIE['session_token']);
    return $sessionData !== null;
}

function get_user_data() {
    global $redisClient;

    if (!isset($_COOKIE['session_token'])) {
        return null;
    }

    return $redisClient->getSession($_COOKIE['session_token']);
}
