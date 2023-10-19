<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\Data;
use App\Service\CategoryService;
use App\Service\ManufacturerService;
use App\Service\ProductService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DataHandler
{
    public function __construct(
        private readonly ProductService      $productService,
        private readonly CategoryService     $categoryService,
        private readonly ManufacturerService $manufacturerService
    )
    {
    }

    public function __invoke(Data $data): void
    {
        $productName = $data->getProduct();
        $productPrice = $data->getPrice();
        $categoryName = $data->getCategory();
        $manufacturerCompany = $data->getManufacturerCompany();
        $manufacturerTaxId = $data->getManufacturerTaxId();
        $manufacturerCity = $data->getManufacturerCity();
        $manufacturerPostcode = $data->getManufacturerPostcode();
        $manufacturerStreet = $data->getManufacturerStreet();
        $manufacturerStreetNumber = $data->getManufacturerStreetNumber();

        $manufacturer = $this->manufacturerService->getManufacturerByTaxId($manufacturerTaxId);
        if (!$manufacturer) {
            $manufacturer = $this->manufacturerService->createManufacturer(
                $manufacturerCompany,
                $manufacturerTaxId,
                $manufacturerCity,
                $manufacturerPostcode,
                $manufacturerStreet,
                $manufacturerStreetNumber
            );
            $this->manufacturerService->saveManufacturer($manufacturer);
        }

        $category = $this->categoryService->getCategoryByName($categoryName);
        if (!$category) {
            $category = $this->categoryService->createCategory($categoryName);
            $this->categoryService->saveCategory($category);
        }

        $product = $this->productService->getProductByName($productName);
        if (!$product) {
            $product = $this->productService->createProduct($productName, $productPrice, $category, $manufacturer);
            $this->productService->saveProduct($product);
        }
    }
}
