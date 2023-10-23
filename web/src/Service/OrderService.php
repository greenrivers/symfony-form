<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;

class OrderService
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function saveOrder(Order $order): void
    {
        $this->orderRepository->add($order, true);
    }
}
