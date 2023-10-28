<?php

declare(strict_types=1);

namespace App\Tests\Integration\Command;

use App\Message\Data;
use org\bovigo\vfs\vfsStream;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ImportDataCommandTest extends KernelTestCase
{
    private Application $application;

    private InMemoryTransport $transport;

    private string $filePath;

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->application = new Application(static::$kernel);
        $this->transport = $container->get('messenger.transport.async');

        $rootDir = vfsStream::setup();
        $structure = [
            'files' => [
                'test.csv' => $this->getCsvContent()
            ]
        ];
        vfsStream::create($structure, $rootDir);
        $this->filePath = vfsStream::url('root/files/test.csv');
    }

    /**
     * @covers ImportDataCommand::execute
     */
    public function testExecute(): void
    {
        $command = $this->application->find('app:import-data');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'filePath' => $this->filePath
        ]);

        $envelopes = $this->transport->get();
        $data = reset($envelopes)->getMessage();

        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('File import success.', $commandTester->getDisplay());

        $this->assertCount(1, $envelopes);
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

    private function getCsvContent(): string
    {
        $data = [
            [
                'id',
                'product',
                'category',
                'manufacturer_company',
                'manufacturer_tax_id',
                'manufacturer_city',
                'manufacturer_postcode',
                'manufacturer_street',
                'manufacturer_street_number'
            ],
            [
                '1',
                'Test product',
                '99.99',
                'Test category',
                'Test manufacturer company',
                '123456789',
                'Test manufacturer city',
                '12-345',
                'Test manufacturer street',
                '10'
            ]
        ];

        return implode(
            PHP_EOL,
            array_map(static fn($row) => implode(',', $row), $data)
        );
    }
}
