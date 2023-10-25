<?php

declare(strict_types=1);

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[Embeddable]
class Address
{
    #[ORM\Column(length: 64)]
    #[Assert\NotBlank]
    #[Groups(['manufacturer_read', 'manufacturer_write'])]
    private string $city;

    #[ORM\Column(length: 32)]
    #[Assert\NotBlank]
    #[Groups(['manufacturer_read', 'manufacturer_write'])]
    private string $postcode;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank]
    #[Groups(['manufacturer_read', 'manufacturer_write'])]
    private string $street;

    #[ORM\Column(length: 8)]
    #[Assert\NotBlank]
    #[Groups(['manufacturer_read', 'manufacturer_write'])]
    private string $streetNumber;

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }
}
