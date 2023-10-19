<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function createProduct(string $name, string $price, Category $category, Manufacturer $manufacturer): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setCategory($category);
        $product->setManufacturer($manufacturer);

        return $product;
    }

    public function saveProduct(Product $product): void
    {
        $this->productRepository->add($product, true);
    }

    public function getProductByName(string $name): ?Product
    {
        return $this->productRepository->findOneByName($name);
    }
}
