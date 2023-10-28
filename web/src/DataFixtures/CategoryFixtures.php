<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category';

    public function load(ObjectManager $manager): void
    {
        $categoriesData = [
            ['name' => 'Test category'],
            ['name' => 'Test category 2'],
            ['name' => 'Test category 3']
        ];

        foreach ($categoriesData as $index => $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);

            $this->addReference(self::CATEGORY_REFERENCE . $index, $category);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
