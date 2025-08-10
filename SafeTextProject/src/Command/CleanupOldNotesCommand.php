<?php

namespace App\Command;

use PDO;

class CleanupOldNotesCommand
{
    private $pdoProvider;

    public function __construct(callable $pdoProvider)
    {
        $this->pdoProvider = $pdoProvider;
    }

    public function getName(): string
    {
        return 'app:cleanup-old-notes';
    }

    public function execute(): int
    {
        try {
            $pdo = ($this->pdoProvider)();
            $stmt = $pdo->prepare('DELETE FROM notes WHERE created_at < (NOW() - INTERVAL 24 HOUR)');
            $stmt->execute();
            return 0;
        } catch (\Throwable $e) {
            fwrite(STDERR, 'cleanup failed' . PHP_EOL);
            return 1;
        }
    }
}

