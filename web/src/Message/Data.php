<?php

declare(strict_types=1);

namespace App\Message;

class Data
{
    public function __construct(
        private string $product,
        private string $price,
        private string $category,
        private string $manufacturerCompany,
        private string $manufacturerTaxId,
        private string $manufacturerCity,
        private string $manufacturerPostcode,
        private string $manufacturerStreet,
        private string $manufacturerStreetNumber
    )
    {
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function setProduct(string $product): void
    {
        $this->product = $product;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getManufacturerCompany(): string
    {
        return $this->manufacturerCompany;
    }

    public function setManufacturerCompany(string $manufacturerCompany): void
    {
        $this->manufacturerCompany = $manufacturerCompany;
    }

    public function getManufacturerTaxId(): string
    {
        return $this->manufacturerTaxId;
    }

    public function setManufacturerTaxId(string $manufacturerTaxId): void
    {
        $this->manufacturerTaxId = $manufacturerTaxId;
    }

    public function getManufacturerCity(): string
    {
        return $this->manufacturerCity;
    }

    public function setManufacturerCity(string $manufacturerCity): void
    {
        $this->manufacturerCity = $manufacturerCity;
    }

    public function getManufacturerPostcode(): string
    {
        return $this->manufacturerPostcode;
    }

    public function setManufacturerPostcode(string $manufacturerPostcode): void
    {
        $this->manufacturerPostcode = $manufacturerPostcode;
    }

    public function getManufacturerStreet(): string
    {
        return $this->manufacturerStreet;
    }

    public function setManufacturerStreet(string $manufacturerStreet): void
    {
        $this->manufacturerStreet = $manufacturerStreet;
    }

    public function getManufacturerStreetNumber(): string
    {
        return $this->manufacturerStreetNumber;
    }

    public function setManufacturerStreetNumber(string $manufacturerStreetNumber): void
    {
        $this->manufacturerStreetNumber = $manufacturerStreetNumber;
    }
}