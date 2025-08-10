<?php

namespace SecureLogin\Storage;

use SecureLogin\Auth\User;

class UserRepository
{
    private array $users;

    public function __construct()
    {
        $this->users = [
            'admin' => new User(1, 'admin', '123456', 'Administrator', 'admin'),
        ];
    }

    public function findByUsername(string $username): ?User
    {
        return $this->users[$username] ?? null;
    }
}


