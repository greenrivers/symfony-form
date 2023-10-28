<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Embeddable\Address;
use App\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ManufacturerFixtures extends Fixture
{
    public const MANUFACTURER_REFERENCE = 'manufacturer';

    public function load(ObjectManager $manager): void
    {
        $manufacturersData = [
            [
                'company' => 'Manufacturer company',
                'tax_id' => '111111111',
                'address' => [
                    'city' => 'Manufacturer city',
                    'postcode' => '12-345',
                    'street' => 'Manufacturer street',
                    'street_number' => '10'
                ]
            ],
            [
                'company' => 'Manufacturer company 2',
                'tax_id' => '222222222',
                'address' => [
                    'city' => 'Manufacturer city 2',
                    'postcode' => '23-456',
                    'street' => 'Manufacturer street 2',
                    'street_number' => '20'
                ]
            ],
            [
                'company' => 'Manufacturer company 3',
                'tax_id' => '333333333',
                'address' => [
                    'city' => 'Manufacturer city 3',
                    'postcode' => '34-567',
                    'street' => 'Manufacturer street 3',
                    'street_number' => '30'
                ]
            ]
        ];

        foreach ($manufacturersData as $index => $manufacturerData) {
            $address = new Address();
            $address->setCity($manufacturerData['address']['city'])
                ->setPostcode($manufacturerData['address']['postcode'])
                ->setStreet($manufacturerData['address']['street'])
                ->setStreetNumber($manufacturerData['address']['street_number']);

            $manufacturer = new Manufacturer();
            $manufacturer->setCompany($manufacturerData['company'])
                ->setTaxId($manufacturerData['tax_id'])
                ->setAddress($address);

            $this->addReference(self::MANUFACTURER_REFERENCE . $index, $manufacturer);

            $manager->persist($manufacturer);
        }

        $manager->flush();
    }
}
