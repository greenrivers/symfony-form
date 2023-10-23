<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Customer;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;

class OrderDto
{
    public function __construct(
        public readonly DateTimeInterface $dateTime,
        public readonly Customer          $customer,
        public readonly Collection        $orderItems,
        public Category                   $category
    )
    {
    }
}
