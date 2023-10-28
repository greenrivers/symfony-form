<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Type;

use App\Dto\OrderDto;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Embeddable\Address;
use App\Entity\Manufacturer;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Form\DataMapper\CategoryDataMapper;
use App\Form\Type\Order\CategoryType;
use App\Form\Type\OrderType;
use App\Service\CategoryService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class OrderTypeTest extends TypeTestCase
{
    private CategoryService|MockObject $categoryServiceMock;

    protected function setUp(): void
    {
        $this->categoryServiceMock = $this->createMock(CategoryService::class);

        parent::setUp();
    }

    public function testSubmitValidData(): void
    {
        $dateTime = new DateTimeImmutable();
        $products = $this->getProducts();
        $category = $this->getCategory($products);
        $orderDto = $this->getOrderDto($dateTime, new Customer(), $category);

        $this->categoryServiceMock->expects(self::once())
            ->method('getCategoryById')
            ->with(1)
            ->willReturn($category);

        $formData = [
            'customer' => [
                'email' => 'test@email.com'
            ],
            'category' => [
                'name' => '1'
            ],
            'orderItems' => [
                [
                    'product' => 0,
                    'quantity' => 5
                ],
                [
                    'product' => 1,
                    'quantity' => 10
                ]
            ]
        ];

        $form = $this->factory->create(
            OrderType::class,
            $orderDto,
            [
                'products' => $products
            ]
        );

        $expectedData = new OrderDto($dateTime, new Customer(), new ArrayCollection(), $category);
        $expectedData->customer->setEmail($formData['customer']['email']);
        $expectedData->category->setName($formData['category']['name']);
        $orderItem = new OrderItem();
        $orderItem->setProduct($products->get(0))
            ->setQuantity(5);
        $orderItem2 = new OrderItem();
        $orderItem2->setProduct($products->get(1))
            ->setQuantity(10);
        $expectedData->orderItems->add($orderItem);
        $expectedData->orderItems->add($orderItem2);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedData, $orderDto);
    }

    public function testFormView(): void
    {
        $dateTime = new DateTimeImmutable();
        $customer = new Customer();
        $products = $this->getProducts();
        $category = $this->getCategory($products);
        $orderDto = $this->getOrderDto($dateTime, $customer, $category);

        $form = $this->factory->create(
            OrderType::class,
            $orderDto,
            [
                'products' => $products
            ]
        );
        $view = $form->createView();

        $this->assertEquals('order', $view->vars['id']);
        $this->assertEquals('order', $view->vars['name']);
        $this->assertObjectHasProperty('customer', $view->vars['value']);
        $this->assertObjectHasProperty('orderItems', $view->vars['value']);
        $this->assertEquals($category, $view->vars['value']->category);
        $this->assertEquals($dateTime, $view->vars['value']->dateTime);
    }

    protected function getExtensions(): array
    {
        $abstractQueryMock = $this->createMock(AbstractQuery::class);
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $entityManagerMock = $this->createMock(EntityManager::class);
        $categoryDataMapperMock = $this->createMock(CategoryDataMapper::class);
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);

        $abstractQueryMock->method('execute')
            ->willReturn([]);
        $queryBuilderMock->method('getQuery')
            ->willReturn($abstractQueryMock);
        $entityRepositoryMock->method('createQueryBuilder')
            ->willReturn($queryBuilderMock);
        $entityManagerMock->method('getClassMetadata')
            ->willReturn(new ClassMetadata(Category::class));
        $entityManagerMock->method('getRepository')
            ->willReturn($entityRepositoryMock);
        $managerRegistryMock->method('getManagerForClass')
            ->willReturn($entityManagerMock);

        return [
            new PreloadedExtension([
                new OrderType($this->categoryServiceMock),
                new CategoryType($categoryDataMapperMock),
                new EntityType($managerRegistryMock)
            ], [])
        ];
    }

    private function getOrderDto(DateTimeImmutable $dateTime, Customer $customer, Category $category): OrderDto
    {
        return new OrderDto($dateTime, $customer, new ArrayCollection(), $category);
    }

    private function getProducts(): Collection
    {
        $address = new Address();
        $address->setCity('Manufacturer city')
            ->setPostcode('12-345')
            ->setStreet('Manufacturer street')
            ->setStreetNumber('10');
        $manufacturer = new Manufacturer();
        $manufacturer->setCompany('Manufacturer company')
            ->setTaxId('123456789')
            ->setAddress($address);
        $product = new Product();
        $product->setName('Test product')
            ->setPrice('9.99')
            ->setManufacturer($manufacturer);
        $product2 = new Product();
        $product2->setName('Test product 2')
            ->setPrice('50')
            ->setManufacturer($manufacturer);

        return new ArrayCollection([$product, $product2]);
    }

    private function getCategory(Collection $products): Category
    {
        $category = new Category();
        $category->setName('Test category')
            ->addProduct($products->get(0))
            ->addProduct($products->get(1));

        return $category;
    }
}
