<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\OrderItem;
use App\Form\Type\Order\OrderItemType;
use App\Form\Type\OrderType;
use App\Service\CategoryService;
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

    #[Route('/form', name: self::ORDER_FORM_ROUTE, methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function orderForm(Request $request, CategoryService $categoryService): Response
    {
        $category = $categoryService->getFirstCategory();
        $products = $category?->getProducts();
        $form = $this->createForm(
            OrderType::class,
            null,
            [
                'products' => $products
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();

            // TODO: save entities

            $this->addFlash('success', 'Article créé avec succès !');
            return $this->redirectToRoute(self::ORDER_SUCCESS_ROUTE);
        }

        return $this->render('order/form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/success', name: self::ORDER_SUCCESS_ROUTE, methods: [Request::METHOD_GET])]
    public function orderSuccess(Request $request): Response
    {
        return $this->render('order/success.html.twig', [
        ]);
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
