<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Message\Data;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DataDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): Data
    {
        [
            ,
            $product,
            $price,
            $category,
            $manufacturerCompany,
            $manufacturerTaxId,
            $manufacturerCity,
            $manufacturerPostcode,
            $manufacturerStreet,
            $manufacturerStreetNumber
        ] = $data;

        return new $type(
            $product,
            $price,
            $category,
            $manufacturerCompany,
            $manufacturerTaxId,
            $manufacturerCity,
            $manufacturerPostcode,
            $manufacturerStreet,
            $manufacturerStreetNumber
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return Data::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Data::class => true
        ];
    }
}
