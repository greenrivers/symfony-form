<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Embeddable\Address;
use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;

class ManufacturerService
{
    public function __construct(private readonly ManufacturerRepository $manufacturerRepository)
    {
    }

    public function createManufacturer(
        string $company,
        string $taxId,
        string $city,
        string $postcode,
        string $street,
        string $streetNumber
    ): Manufacturer
    {
        $address = new Address();
        $manufacturer = new Manufacturer();

        $address->setCity($city)
            ->setPostcode($postcode)
            ->setStreet($street)
            ->setStreetNumber($streetNumber);
        $manufacturer->setCompany($company)
            ->setTaxId($taxId)
            ->setAddress($address);

        return $manufacturer;
    }

    public function saveManufacturer(Manufacturer $manufacturer): void
    {
        $this->manufacturerRepository->add($manufacturer, true);
    }

    public function getManufacturerByTaxId(string $taxId): ?Manufacturer
    {
        return $this->manufacturerRepository->findOneByTaxId($taxId);
    }
}
