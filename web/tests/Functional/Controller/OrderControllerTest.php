<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Controller\OrderController;
use App\DataFixtures\ProductFixtures;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');
        /** @var DatabaseToolCollection $databaseToolCollection */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);

        $this->entityManager = $doctrine->getManager();
        $this->databaseTool = $databaseToolCollection->get();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()
            ->getAllMetadata();

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    /**
     * @covers OrderController::order
     */
    public function testOrder(): void
    {
        $this->databaseTool->loadFixtures([ProductFixtures::class]);

        $crawler = $this->client->request('GET', '/order/');
        $form = $crawler->selectButton('Submit')->form();

        $values = $form->getPhpValues();
        $values['order']['customer']['email'] = 'test@email.com';
        $values['order']['category']['name'] = 2;
        $values['order']['orderItems'][0]['product'] = 2;
        $values['order']['orderItems'][0]['quantity'] = 5;
        $values['order']['orderItems'][1]['product'] = 3;
        $values['order']['orderItems'][1]['quantity'] = 10;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $crawler = $this->client->followRedirect();

        /** @var Order $order */
        $order = $this->entityManager->getRepository(Order::class)
            ->createQueryBuilder('o')
            ->join('o.customer', 'c')
            ->where("c.email = 'test@email.com'")
            ->getQuery()
            ->getSingleResult();
        /** @var Product[] $products */
        $products = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = 2')
            ->getQuery()
            ->execute();

        $this->assertCount(1, $crawler->filter('.alert-success'));
        $this->assertStringContainsString('Order saved!', $crawler->filter('.alert-success')->text());

        $this->assertEquals('test@email.com', $order->getCustomer()->getEmail());
        $this->assertCount(2, $order->getOrderItems());
        $this->assertEquals($products[0], $order->getOrderItems()->get(0)->getProduct());
        $this->assertEquals(5, $order->getOrderItems()->get(0)->getQuantity());
        $this->assertEquals($products[1], $order->getOrderItems()->get(1)->getProduct());
        $this->assertEquals(10, $order->getOrderItems()->get(1)->getQuantity());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
