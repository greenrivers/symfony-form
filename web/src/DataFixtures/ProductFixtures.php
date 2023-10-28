<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Manufacturer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $productsData = [
            [
                'name' => 'Test product',
                'price' => '12.99',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 0,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 0
            ],
            [
                'name' => 'Test product 2',
                'price' => '99.99',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 0,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 1
            ],
            [
                'name' => 'Test product 3',
                'price' => '37.49',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 0,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 1
            ],
            [
                'name' => 'Test product 4',
                'price' => '129.99',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 1,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 2
            ],
            [
                'name' => 'Test product 5',
                'price' => '100.00',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 1,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 2
            ],
            [
                'name' => 'Test product 6',
                'price' => '50.00',
                'manufacturer_reference' => ManufacturerFixtures::MANUFACTURER_REFERENCE . 2,
                'category_reference' => CategoryFixtures::CATEGORY_REFERENCE . 2
            ]
        ];

        foreach ($productsData as $productData) {
            $manufacturer = $this->getReference($productData['manufacturer_reference'], Manufacturer::class);
            $category = $this->getReference($productData['category_reference'], Category::class);

            $product = new Product();
            $product->setName($productData['name'])
                ->setPrice($productData['price'])
                ->setManufacturer($manufacturer)
                ->setCategory($category);

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ManufacturerFixtures::class,
            CategoryFixtures::class
        ];
    }
}
