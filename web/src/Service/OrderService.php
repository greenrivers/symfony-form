<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OrderDto;
use App\Entity\Order;
use App\Repository\OrderRepository;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly CustomerService $customerService
    )
    {
    }

    public function createOrder(OrderDto $orderDto): Order
    {
        $order = new Order();
        $order->setDateTime($orderDto->dateTime);

        $customer = $orderDto->customer;
        $customer = $this->customerService->getCustomerByEmail($customer->getEmail()) ?: $customer;
        $order->setCustomer($customer);

        foreach ($orderDto->orderItems as $orderItem) {
            $order->addOrderItem($orderItem);
        }

        return $order;
    }

    public function saveOrder(Order $order): void
    {
        $this->orderRepository->add($order, true);
    }
}
