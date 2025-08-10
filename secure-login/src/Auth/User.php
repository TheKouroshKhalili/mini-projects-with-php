<?php

namespace SecureLogin\Auth;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public string $name;
    public string $role;

    public function __construct(int $id, string $username, string $password, string $name, string $role)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }
}


