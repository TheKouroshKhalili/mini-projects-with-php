<?php
require __DIR__ . '/vendor/autoload.php';
use App\Support\Database as SupportDatabase;

class Database {
    public static function create(): ?PDO
    {
        return SupportDatabase::create();
    }
}

?>