<?php

namespace App;

use App\Services\DatabaseService;
use App\Services\RedisService;
use App\Services\FileLogger;

class CreateOrderService
{
    private DatabaseService $db;
    private RedisService $redis;
    private FileLogger $logger;

    public function __construct(DatabaseService $db, RedisService $redis, FileLogger $logger)
    {
        $this->db = $db;
        $this->redis = $redis;
        $this->logger = $logger;
    }

    public function execute(Order $order): void
    {
        $this->db->saveOrder($order);
        $this->redis->incrementTotalOrders();
        $this->logger->log('Order created: ' . $order->customerName . ' | ' . $order->amount);
    }
}


