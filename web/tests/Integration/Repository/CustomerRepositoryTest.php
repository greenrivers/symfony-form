<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\DataFixtures\CustomerFixtures;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerRepositoryTest extends KernelTestCase
{
    private CustomerRepository $customerRepository;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->customerRepository = $container->get(CustomerRepository::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers CustomerRepository::findOneByEmail
     */
    public function testFindOneByEmail(): void
    {
        $this->databaseTool->loadFixtures([CustomerFixtures::class]);

        $customer = $this->customerRepository->findOneByEmail('test3@email.com');

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('test3@email.com', $customer->getEmail());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
