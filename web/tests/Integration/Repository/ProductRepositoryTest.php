<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\DataFixtures\ProductFixtures;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    private ProductRepository $productRepository;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->productRepository = $container->get(ProductRepository::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers ProductRepository::findOneByName
     */
    public function testFindOneByName(): void
    {
        $this->databaseTool->loadFixtures([ProductFixtures::class]);

        $product = $this->productRepository->findOneByName('Test product 5');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test product 5', $product->getName());
        $this->assertEquals('100.00', $product->getPrice());
        $this->assertEquals('Test category 3', $product->getCategory()->getName());
        $this->assertEquals('Manufacturer company 2', $product->getManufacturer()->getCompany());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
