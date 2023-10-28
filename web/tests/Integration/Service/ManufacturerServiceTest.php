<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\DataFixtures\ManufacturerFixtures;
use App\Entity\Embeddable\Address;
use App\Entity\Manufacturer;
use App\Service\ManufacturerService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ManufacturerServiceTest extends KernelTestCase
{
    private ManufacturerService $manufacturerService;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->manufacturerService = $container->get(ManufacturerService::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers ManufacturerService::createManufacturer
     */
    public function testCreateManufacturer(): void
    {
        $company = 'Manufacturer company';
        $taxId = '123456789';
        $city = 'Manufacturer city';
        $postcode = '12-345';
        $street = 'Manufacturer street';
        $streetNumber = '10';

        $manufacturer = $this->manufacturerService->createManufacturer(
            $company,
            $taxId,
            $city,
            $postcode,
            $street,
            $streetNumber
        );

        $this->assertInstanceOf(Manufacturer::class, $manufacturer);
        $this->assertInstanceOf(Address::class, $manufacturer->getAddress());
        $this->assertEquals($company, $manufacturer->getCompany());
        $this->assertEquals($taxId, $manufacturer->getTaxId());
        $this->assertEquals($city, $manufacturer->getAddress()->getCity());
        $this->assertEquals($postcode, $manufacturer->getAddress()->getPostcode());
        $this->assertEquals($street, $manufacturer->getAddress()->getStreet());
        $this->assertEquals($streetNumber, $manufacturer->getAddress()->getStreetNumber());
    }

    /**
     * @covers ManufacturerService::saveManufacturer
     */
    public function testSaveManufacturer(): void
    {
        $embeddable = new Address();
        $embeddable->setCity('Manufacturer city')
            ->setPostcode('12-345')
            ->setStreet('Manufacturer street')
            ->setStreetNumber('10');
        $entity = new Manufacturer();
        $entity->setCompany('Manufacturer company')
            ->setTaxId('123456789')
            ->setAddress($embeddable);

        $this->manufacturerService->saveManufacturer($entity);

        $manufacturer = $this->entityManager->getRepository(Manufacturer::class)
            ->findOneBy(['company' => 'Manufacturer company']);

        $this->assertNotNull($manufacturer);
        $this->assertEquals('Manufacturer company', $manufacturer->getCompany());
        $this->assertEquals('123456789', $manufacturer->getTaxId());
        $this->assertEquals('Manufacturer city', $manufacturer->getAddress()->getCity());
        $this->assertEquals('12-345', $manufacturer->getAddress()->getPostcode());
        $this->assertEquals('Manufacturer street', $manufacturer->getAddress()->getStreet());
        $this->assertEquals('10', $manufacturer->getAddress()->getStreetNumber());
    }

    /**
     * @covers ManufacturerService::getManufacturerByTaxId
     */
    public function testGetManufacturerByTaxId(): void
    {
        $this->databaseTool->loadFixtures([ManufacturerFixtures::class]);

        $manufacturer = $this->manufacturerService->getManufacturerByTaxId('333333333');

        $this->assertInstanceOf(Manufacturer::class, $manufacturer);
        $this->assertEquals('Manufacturer company 3', $manufacturer->getCompany());
        $this->assertEquals('333333333', $manufacturer->getTaxId());
        $this->assertEquals('Manufacturer city 3', $manufacturer->getAddress()->getCity());
        $this->assertEquals('34-567', $manufacturer->getAddress()->getPostcode());
        $this->assertEquals('Manufacturer street 3', $manufacturer->getAddress()->getStreet());
        $this->assertEquals('30', $manufacturer->getAddress()->getStreetNumber());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
