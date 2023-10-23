<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Customer;
use DateTimeInterface;

class OrderDto
{
    public function __construct(
        public readonly DateTimeInterface $dateTime,
        public readonly Customer          $customer,
        public Category                   $category,
        public array                      $orderItems
    )
    {
    }
}
