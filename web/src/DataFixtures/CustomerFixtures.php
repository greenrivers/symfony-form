<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customersData = [
            ['email' => 'test@email.com'],
            ['email' => 'test2@email.com'],
            ['email' => 'test3@email.com']
        ];

        foreach ($customersData as $customerData) {
            $customer = new Customer();
            $customer->setEmail($customerData['email']);

            $manager->persist($customer);
        }

        $manager->flush();
    }
}
