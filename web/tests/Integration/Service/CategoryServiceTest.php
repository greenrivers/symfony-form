<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Service\CategoryService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryServiceTest extends KernelTestCase
{
    private CategoryService $categoryService;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->categoryService = $container->get(CategoryService::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers CategoryService::saveCategory
     */
    public function testSaveCategory(): void
    {
        $entity = new Category();
        $entity->setName('Test category');

        $this->categoryService->saveCategory($entity);

        $category = $this->entityManager->getRepository(Category::class)
            ->findOneBy(['name' => 'Test category']);

        $this->assertNotNull($category);
        $this->assertEquals('Test category', $category->getName());
    }

    /**
     * @covers CategoryService::getFirstCategory
     */
    public function testGetFirstCategory(): void
    {
        $this->databaseTool->loadFixtures([CategoryFixtures::class]);

        $category = $this->categoryService->getFirstCategory();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test category', $category->getName());
    }

    /**
     * @covers CategoryService::getCategoryById
     */
    public function testGetCategoryById(): void
    {
        $this->databaseTool->loadFixtures([
            CategoryFixtures::class
        ]);

        $category = $this->categoryService->getCategoryById(2);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test category 2', $category->getName());
    }

    /**
     * @covers CategoryService::getCategoryByName
     */
    public function testGetCategoryByName(): void
    {
        $this->databaseTool->loadFixtures([
            CategoryFixtures::class
        ]);

        $category = $this->categoryService->getCategoryByName('Test category 3');

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test category 3', $category->getName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
