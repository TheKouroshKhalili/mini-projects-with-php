<?php

namespace App\Support;

class SecureSession
{
    public static function startStrict(): void
    {

        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => $cookieParams['path'],
            'domain' => $cookieParams['domain'],
            'secure' => true, 
            'httponly' => true, 
            'samesite' => 'Strict', 
        ]);
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}

