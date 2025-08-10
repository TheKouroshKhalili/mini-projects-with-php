<?php

namespace SecureLogin\Auth;

use SecureLogin\Storage\RedisClient;
use SecureLogin\Storage\UserRepository;

class AuthenticationManager
{
    private RedisClient $redis;
    private UserRepository $userRepo;
    private int $sessionTtl = 1800;
    private int $attemptTtl = 600;
    private int $maxAttempts = 3;

    public function __construct(RedisClient $redis, UserRepository $userRepo, array $authConfig = [])
    {
        $this->redis = $redis;
        $this->userRepo = $userRepo;
        if (isset($authConfig['session_ttl'])) { $this->sessionTtl = (int)$authConfig['session_ttl']; }
        if (isset($authConfig['attempt_ttl'])) { $this->attemptTtl = (int)$authConfig['attempt_ttl']; }
        if (isset($authConfig['max_attempts'])) { $this->maxAttempts = (int)$authConfig['max_attempts']; }
    }

    public function isLocked(string $username): bool
    {
        return $this->redis->getLoginAttempts($username) >= $this->maxAttempts;
    }

    public function getLockRemaining(string $username): int
    {
        return $this->redis->getLockTimeRemaining($username);
    }

    public function login(string $username, string $password, string $ip): array
    {
        if ($this->isLocked($username)) {
            $ttl = $this->getLockRemaining($username);
            return ['ok' => false, 'message' => "حساب شما قفل است. {$ttl} ثانیه دیگر دوباره تلاش کنید."];
        }

        $user = $this->userRepo->findByUsername($username);
        if ($user === null || $user->password !== $password) {
            $attempts = $this->redis->incrementLoginAttempts($username, $this->attemptTtl);
            if ($attempts >= $this->maxAttempts) {
                $ttl = $this->getLockRemaining($username);
                return ['ok' => false, 'message' => "تعداد تلاش‌های شما به پایان رسید. حساب شما {$ttl} ثانیه قفل شد."];
            }
            return ['ok' => false, 'message' => 'نام کاربری یا رمز عبور اشتباه است.'];
        }

        $token = bin2hex(random_bytes(16));
        $sessionData = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'role' => $user->role,
            'ip' => $ip,
        ];
        $this->redis->saveSession($token, $sessionData, $this->sessionTtl);
        $this->redis->resetLoginAttempts($username);

        return ['ok' => true, 'token' => $token, 'user' => $sessionData];
    }

    public function logout(string $token): void
    {
        $this->redis->deleteSession($token);
    }

    public function check(string $token): ?Session
    {
        $data = $this->redis->getSession($token);
        if ($data === null) {
            return null;
        }
        return new Session($token, $data['id'] ?? 0, $data['username'] ?? '', $data['name'] ?? '', $data['role'] ?? '', $data['ip'] ?? '');
    }
}


