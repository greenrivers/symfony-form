<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ManufacturerFixtures;
use App\DataFixtures\ProductFixtures;
use App\Entity\Category;
use App\Entity\Embeddable\Address;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Service\ProductService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductServiceTest extends KernelTestCase
{
    private ProductService $productService;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->productService = $container->get(ProductService::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers ProductService::createProduct
     */
    public function testCreateProduct(): void
    {
        $name = 'Test product';
        $price = '29.99';
        $category = new Category();
        $category->setName('Test category');
        $address = new Address();
        $address->setCity('Manufacturer city')
            ->setPostcode('12-345')
            ->setStreet('Manufacturer street')
            ->setStreetNumber('10');
        $manufacturer = new Manufacturer();
        $manufacturer->setCompany('Manufacturer company')
            ->setTaxId('123456789')
            ->setAddress($address);

        $product = $this->productService->createProduct($name, $price, $category, $manufacturer);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
        $this->assertEquals($category, $product->getCategory());
        $this->assertEquals($manufacturer, $product->getManufacturer());
    }

    /**
     * @covers ProductService::saveProduct
     */
    public function testSaveProduct(): void
    {
        $this->databaseTool->loadFixtures([
            CategoryFixtures::class,
            ManufacturerFixtures::class
        ]);

        $categoryEntity = $this->entityManager->getRepository(Category::class)
            ->findOneBy(['name' => 'Test category']);
        $manufacturerEntity = $this->entityManager->getRepository(Manufacturer::class)
            ->findOneBy(['company' => 'Manufacturer company']);
        $entity = new Product();
        $entity->setName('Test product')
            ->setPrice('19.99')
            ->setCategory($categoryEntity)
            ->setManufacturer($manufacturerEntity);

        $this->productService->saveProduct($entity);

        $product = $this->entityManager->getRepository(Product::class)
            ->findOneBy(['name' => 'Test product']);

        $this->assertNotNull($product);
        $this->assertEquals('Test product', $product->getName());
        $this->assertEquals('19.99', $product->getPrice());
        $this->assertEquals($categoryEntity, $product->getCategory());
        $this->assertEquals($manufacturerEntity, $product->getManufacturer());
    }

    /**
     * @covers ProductService::getProductByName
     */
    public function testGetProductByName(): void
    {
        $this->databaseTool->loadFixtures([ProductFixtures::class]);

        $product = $this->productService->getProductByName('Test product 4');

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test product 4', $product->getName());
        $this->assertEquals('129.99', $product->getPrice());
        $this->assertEquals('Test category 3', $product->getCategory()->getName());
        $this->assertEquals('Manufacturer company 2', $product->getManufacturer()->getCompany());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
