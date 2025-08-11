<?php

namespace App\Services;

use App\Order;
use PDO;

class DatabaseService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS orders (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_name TEXT NOT NULL, amount REAL NOT NULL, created_at TEXT NOT NULL)');
    }

    public function saveOrder(Order $order): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO orders (customer_name, amount, created_at) VALUES (:name, :amount, :created_at)');
        return $stmt->execute([
            ':name' => $order->customerName,
            ':amount' => $order->amount,
            ':created_at' => date('c'),
        ]);
    }
}


