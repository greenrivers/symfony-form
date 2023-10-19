<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function createCategory(string $name): Category
    {
        $category = new Category();
        $category->setName($name);

        return $category;
    }

    public function saveCategory(Category $category): void
    {
        $this->categoryRepository->add($category, true);
    }

    public function getCategoryByName(string $name): ?Category
    {
        return $this->categoryRepository->findOneByName($name);
    }
}
