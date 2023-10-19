<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Serializer\Normalizer\DataDenormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as BaseSerializer;
use Symfony\Component\Serializer\SerializerInterface;

class Serializer implements SerializerInterface, NormalizerInterface, DenormalizerInterface
{
    private readonly SerializerInterface $serializer;

    public function __construct()
    {
        $normalizers = [
            new DataDenormalizer(),
            new DateTimeNormalizer(),
            new GetSetMethodNormalizer(),
            new ObjectNormalizer(),
            new ArrayDenormalizer()
        ];
        $encoders = [new JsonEncoder()];

        $this->serializer = new BaseSerializer($normalizers, $encoders);
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): mixed
    {
        return $this->serializer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $this->serializer->supportsNormalization($data, $format, $context);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->serializer->supportsDenormalization($data, $type, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => null,
            '*' => false
        ];
    }
}
