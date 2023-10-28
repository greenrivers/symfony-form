<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryRepositoryTest extends KernelTestCase
{
    private CategoryRepository $categoryRepository;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->categoryRepository = $container->get(CategoryRepository::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers CategoryRepository::findFirst
     */
    public function testFindFirst(): void
    {
        $this->databaseTool->loadFixtures([CategoryFixtures::class]);

        $category = $this->categoryRepository->findFirst();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test category', $category->getName());
    }

    /**
     * @covers CategoryRepository::findOneByName
     */
    public function testFindOneByName(): void
    {
        $this->databaseTool->loadFixtures([CategoryFixtures::class]);

        $category = $this->categoryRepository->findOneByName('Test category 2');

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test category 2', $category->getName());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
