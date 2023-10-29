<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\OrderDto;
use App\Entity\Customer;
use App\Entity\OrderItem;
use App\Form\Type\Order\OrderItemType;
use App\Form\Type\OrderType;
use App\Service\CategoryService;
use App\Service\OrderService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(['/{_locale}/order', '/order'])]
class OrderController extends AbstractController
{
    public const ORDER_ROUTE = 'order';
    public const ORDER_PRODUCTS_ROUTE = 'order_products';

    #[Route(name: self::ORDER_ROUTE, methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function order(
        Request         $request,
        CategoryService $categoryService,
        OrderService    $orderService
    ): Response
    {
        $dateTime = new DateTimeImmutable();
        $customer = new Customer();
        $category = $categoryService->getFirstCategory();
        $products = $category?->getProducts();

        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $orderDto = new OrderDto($dateTime, $customer, new ArrayCollection(), $category);
        $form = $this->createForm(
            OrderType::class,
            $orderDto,
            [
                'products' => $products
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var OrderDto $orderDto */
            $orderDto = $form->getData();

            $order = $orderService->createOrder($orderDto);
            $orderService->saveOrder($order);

            $this->addFlash('success', 'order.message');
            return $this->redirect($request->getUri());
        }

        return $this->render('order/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/products', name: self::ORDER_PRODUCTS_ROUTE, methods: [Request::METHOD_GET])]
    public function products(
        #[MapQueryParameter] string $categoryId,
        CategoryService             $categoryService
    ): Response
    {
        $orderItem = new OrderItem();
        $category = $categoryService->getCategoryById((int)$categoryId);
        $products = $category?->getProducts();

        $form = $this->createForm(
            OrderItemType::class,
            $orderItem,
            [
                'products' => $products
            ]
        );

        return $this->render('order/form/order-item.html.twig', [
            'form' => $form
        ]);
    }
}
