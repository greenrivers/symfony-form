<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service;

use App\DataFixtures\CustomerFixtures;
use App\DataFixtures\ProductFixtures;
use App\Dto\OrderDto;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\OrderService;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderServiceTest extends KernelTestCase
{
    private OrderService $orderService;

    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $container = static::getContainer();
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->orderService = $container->get(OrderService::class);
        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers OrderService::createOrder
     */
    public function testCreateOrder(): void
    {
        $dateTime = new DateTimeImmutable();
        $customer = new Customer();
        $customer->setEmail('test@email.com');
        $product = new Product();
        $product->setName('Test product')
            ->setPrice('79.99');
        $product2 = new Product();
        $product2->setName('Test product 2')
            ->setPrice('99.99');
        $orderItem = new OrderItem();
        $orderItem->setProduct($product)
            ->setQuantity(5);
        $orderItem2 = new OrderItem();
        $orderItem2->setProduct($product2)
            ->setQuantity(10);
        $orderItems = new ArrayCollection([$orderItem, $orderItem2]);
        $category = new Category();
        $category->setName('Test category');

        $orderDto = new OrderDto($dateTime, $customer, $orderItems, $category);
        $order = $this->orderService->createOrder($orderDto);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($dateTime, $order->getDateTime());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals($orderItems, $order->getOrderItems());
        $this->assertObjectNotHasProperty('category', $order);
    }

    /**
     * @covers OrderService::saveOrder
     */
    public function testSaveOrder(): void
    {
        $this->databaseTool->loadFixtures([
            CustomerFixtures::class,
            ProductFixtures::class
        ]);

        $dateTime = new DateTimeImmutable();
        $customer = $this->entityManager->getRepository(Customer::class)
            ->findOneBy(['email' => 'test@email.com']);
        $products = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where("c.name = 'Test category 2'")
            ->getQuery()
            ->execute();
        $orderItem = new OrderItem();
        $orderItem->setProduct($products[0])
            ->setQuantity(5);
        $orderItem2 = new OrderItem();
        $orderItem2->setProduct($products[1])
            ->setQuantity(10);
        $entity = new Order();
        $entity->setDateTime($dateTime)
            ->setCustomer($customer)
            ->addOrderItem($orderItem)
            ->addOrderItem($orderItem2);

        $this->orderService->saveOrder($entity);

        $order = $this->entityManager->getRepository(Order::class)
            ->findOneBy(['dateTime' => $dateTime]);

        $this->assertNotNull($order);
        $this->assertEquals($dateTime, $order->getDateTime());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals($orderItem, $order->getOrderItems()->get(0));
        $this->assertEquals($orderItem2, $order->getOrderItems()->get(1));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
