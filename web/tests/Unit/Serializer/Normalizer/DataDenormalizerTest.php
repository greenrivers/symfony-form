<?php

declare(strict_types=1);

namespace App\Tests\Unit\Serializer\Normalizer;

use App\Message\Data;
use App\Serializer\Normalizer\DataDenormalizer;
use PHPUnit\Framework\TestCase;

class DataDenormalizerTest extends TestCase
{
    private DataDenormalizer $dataDenormalizer;

    protected function setUp(): void
    {
        $this->dataDenormalizer = new DataDenormalizer();
    }

    /**
     * @covers DataDenormalizer::denormalize
     */
    public function testDenormalize(): void
    {
        $data = [
            1,
            'Test product',
            '99.99',
            'Test category',
            'Test manufacturer company',
            '123456789',
            'Test manufacturer city',
            '12-345',
            'Test manufacturer street',
            '10'
        ];

        $data = $this->dataDenormalizer->denormalize($data, Data::class);

        $this->assertInstanceOf(Data::class, $data);
        $this->assertEquals('Test product', $data->getProduct());
        $this->assertEquals('99.99', $data->getPrice());
        $this->assertEquals('Test category', $data->getCategory());
        $this->assertEquals('Test manufacturer company', $data->getManufacturerCompany());
        $this->assertEquals('123456789', $data->getManufacturerTaxId());
        $this->assertEquals('Test manufacturer city', $data->getManufacturerCity());
        $this->assertEquals('12-345', $data->getManufacturerPostcode());
        $this->assertEquals('Test manufacturer street', $data->getManufacturerStreet());
        $this->assertEquals('10', $data->getManufacturerStreetNumber());
    }
}
