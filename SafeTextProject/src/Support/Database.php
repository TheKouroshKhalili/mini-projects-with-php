<?php

namespace App\Support;

use App\Factory\DatabaseFactory;
use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function create(): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }
        $configPath = __DIR__ . '/../../config/database.php';
        self::$instance = DatabaseFactory::createFromConfig($configPath);
        return self::$instance;
    }
}

