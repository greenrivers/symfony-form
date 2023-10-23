<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Order;
use App\Form\Type\Order\CategoryType;
use App\Form\Type\Order\CustomerType;
use App\Form\Type\Order\OrderItemType;
use App\Service\CategoryService;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $products = $options['products'] ?? [];

        $builder
            ->add('customer', CustomerType::class)
            ->add('category', CategoryType::class, [
                'mapped' => false
            ])
            ->add('submit', SubmitType::class);

        $this->addOrderItems($builder, $products);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $categoryId = $data['category']['name'];
            $category = $this->categoryService->getCategoryById((int)$categoryId);
            $products = $category?->getProducts();

            if ($products) {
                $this->addOrderItems($form, $products);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'products' => []
        ]);
    }

    private function addOrderItems(FormBuilderInterface|FormInterface $form, Collection $products): void
    {
        $form->add('orderItems', CollectionType::class, [
            'entry_type' => OrderItemType::class,
            'entry_options' => [
                'products' => $products
            ],
            'allow_add' => true,
            'allow_delete' => true
        ]);
    }
}
