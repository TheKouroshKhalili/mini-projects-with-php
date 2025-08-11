<?php

namespace App;

class Order
{
    public string $customerName;
    public float $amount;

    public function __construct(string $name, float $amount)
    {
        $this->customerName = $name;
        $this->amount = $amount;
    }
}


