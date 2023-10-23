<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Customer;
use App\Repository\CustomerRepository;

class CustomerService
{
    public function __construct(private readonly CustomerRepository $customerRepository)
    {
    }

    public function getCustomerByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findOneByEmail($email);
    }
}
