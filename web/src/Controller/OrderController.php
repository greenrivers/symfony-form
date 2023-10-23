<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\Type\Order\OrderItemType;
use App\Form\Type\OrderType;
use App\Repository\CustomerRepository;
use App\Service\CategoryService;
use App\Service\OrderService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    public const ORDER_FORM_ROUTE = 'order_form';
    public const ORDER_SUCCESS_ROUTE = 'order_success';
    public const ORDER_FORM_PRODUCTS_ROUTE = 'order_form_products';

    #[Route('/', name: self::ORDER_FORM_ROUTE, methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function orderForm(Request $request, CategoryService $categoryService, OrderService $orderService, CustomerRepository $customerRepository): Response
    {
        $order = new Order();
        $order->setDateTime(new DateTimeImmutable());
        $category = $categoryService->getFirstCategory();
        $products = $category?->getProducts();
        $form = $this->createForm(
            OrderType::class,
            $order,
            [
                'products' => $products
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $category = $form->get('category')->get('name')->getData();

            dump($order);
            dump($category);
            exit();

//            $orderService->saveOrder($order);

            $this->addFlash('success', 'Order saved');
            return $this->redirectToRoute(self::ORDER_SUCCESS_ROUTE);
        }

        return $this->render('order/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/success', name: self::ORDER_SUCCESS_ROUTE, methods: [Request::METHOD_GET])]
    public function orderSuccess(): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/products', name: self::ORDER_FORM_PRODUCTS_ROUTE, methods: [Request::METHOD_GET])]
    public function getProducts(#[MapQueryParameter] string $categoryId, CategoryService $categoryService): Response
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
