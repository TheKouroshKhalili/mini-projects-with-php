<?php

namespace SecureLogin\Auth;

class Session
{
    public string $token;
    public int $userId;
    public string $username;
    public string $name;
    public string $role;
    public string $ip;

    public function __construct(string $token, int $userId, string $username, string $name, string $role, string $ip)
    {
        $this->token = $token;
        $this->userId = $userId;
        $this->username = $username;
        $this->name = $name;
        $this->role = $role;
        $this->ip = $ip;
    }
}


