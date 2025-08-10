<?php

namespace App\Console;

use App\Command\CleanupOldNotesCommand;
use App\Factory\DatabaseFactory;

class Application
{
    private array $commands = [];

    public function __construct()
    {
        $configPath = __DIR__ . '/../../config/database.php';
        $pdoProvider = function () use ($configPath) {
            return DatabaseFactory::createFromConfig($configPath);
        };
        $this->register(new CleanupOldNotesCommand($pdoProvider));
    }

    public function run(array $argv): int
    {
        $name = $argv[1] ?? 'list';
        if ($name === 'list') {
            echo "app:cleanup-old-notes" . PHP_EOL;
            return 0;
        }
        foreach ($this->commands as $command) {
            if ($command->getName() === $name) {
                return $command->execute();
            }
        }
        fwrite(STDERR, 'command not found' . PHP_EOL);
        return 1;
    }

    private function register(object $command): void
    {
        $this->commands[] = $command;
    }
}

