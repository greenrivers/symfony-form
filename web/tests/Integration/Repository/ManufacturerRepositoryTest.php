<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\DataFixtures\ManufacturerFixtures;
use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ManufacturerRepositoryTest extends KernelTestCase
{
    private ManufacturerRepository $manufacturerRepository;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->manufacturerRepository = $container->get(ManufacturerRepository::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers ManufacturerRepository::findOneByTaxId
     */
    public function testFindOneByTaxId(): void
    {
        $this->databaseTool->loadFixtures([ManufacturerFixtures::class]);

        $manufacturer = $this->manufacturerRepository->findOneByTaxId('222222222');

        $this->assertInstanceOf(Manufacturer::class, $manufacturer);
        $this->assertEquals('Manufacturer company 2', $manufacturer->getCompany());
        $this->assertEquals('222222222', $manufacturer->getTaxId());
        $this->assertEquals('Manufacturer city 2', $manufacturer->getAddress()->getCity());
        $this->assertEquals('23-456', $manufacturer->getAddress()->getPostcode());
        $this->assertEquals('Manufacturer street 2', $manufacturer->getAddress()->getStreet());
        $this->assertEquals('20', $manufacturer->getAddress()->getStreetNumber());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
