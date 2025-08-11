<?php

namespace App\Services;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function incrementTotalOrders(): void
    {
        $this->redis->incr('total_orders');
    }
}


