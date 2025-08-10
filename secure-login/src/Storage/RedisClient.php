<?php

namespace SecureLogin\Storage;

class RedisClient
{
    private \Redis $client;
    private string $sessionKeyPrefix = 'session:';
    private string $attemptsKeyPrefix = 'auth:attempts:';
    private string $logsKey = 'auth:logs';

    public function __construct(string $host = '127.0.0.1', int $port = 6379)
    {
        $this->client = new \Redis();
        $this->client->connect($host, $port);
    }

    public function saveSession(string $token, array $data, int $ttl): void
    {
        $key = $this->sessionKeyPrefix . $token;
        $this->client->set($key, json_encode($data), $ttl);
    }

    public function getSession(string $token): ?array
    {
        $key = $this->sessionKeyPrefix . $token;
        $value = $this->client->get($key);
        if ($value === false) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function deleteSession(string $token): void
    {
        $key = $this->sessionKeyPrefix . $token;
        $this->client->del($key);
    }

    public function getLoginAttempts(string $username): int
    {
        $key = $this->attemptsKeyPrefix . $username;
        $value = $this->client->get($key);
        return (int) $value;
    }

    public function incrementLoginAttempts(string $username, int $ttl): int
    {
        $key = $this->attemptsKeyPrefix . $username;
        $attempts = (int) $this->client->incr($key);
        if ($attempts === 1) {
            $this->client->expire($key, $ttl);
        } else {
            $currentTtl = $this->client->ttl($key);
            if ($currentTtl < 0) {
                $this->client->expire($key, $ttl);
            }
        }
        return $attempts;
    }

    public function resetLoginAttempts(string $username): void
    {
        $key = $this->attemptsKeyPrefix . $username;
        $this->client->del($key);
    }

    public function getLockTimeRemaining(string $username): int
    {
        $key = $this->attemptsKeyPrefix . $username;
        $ttl = $this->client->ttl($key);
        return $ttl > 0 ? $ttl : 0;
    }

    public function logAuthAttempt(string $logMessage): void
    {
        $this->client->lPush($this->logsKey, $logMessage);
    }

    public function listSessionTokens(): array
    {
        $keys = $this->client->keys($this->sessionKeyPrefix . '*');
        $tokens = [];
        foreach ($keys as $key) {
            $tokens[] = substr($key, strlen($this->sessionKeyPrefix));
        }
        return $tokens;
    }

    public function deleteAllSessions(): int
    {
        $keys = $this->client->keys($this->sessionKeyPrefix . '*');
        $deleted = 0;
        foreach ($keys as $key) {
            $deleted += (int) $this->client->del($key);
        }
        return $deleted;
    }

    public function listAttemptEntries(): array
    {
        $keys = $this->client->keys($this->attemptsKeyPrefix . '*');
        $result = [];
        foreach ($keys as $key) {
            $username = substr($key, strlen($this->attemptsKeyPrefix));
            $attempts = (int) $this->client->get($key);
            $ttl = (int) $this->client->ttl($key);
            $result[] = [
                'username' => $username,
                'attempts' => $attempts,
                'ttl' => $ttl > 0 ? $ttl : 0,
            ];
        }
        return $result;
    }

    public function deleteAllLocks(): int
    {
        $keys = $this->client->keys($this->attemptsKeyPrefix . '*');
        $deleted = 0;
        foreach ($keys as $key) {
            $deleted += (int) $this->client->del($key);
        }
        return $deleted;
    }
}


