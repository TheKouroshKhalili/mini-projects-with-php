<?php

namespace App\Services;

class FileLogger
{
    private string $logFilePath;

    public function __construct(string $logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    public function log(string $message): void
    {
        $dir = dirname($this->logFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($this->logFilePath, '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND);
    }
}


