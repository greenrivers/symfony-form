<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Type\Order\CategoryType;
use App\Form\Type\Order\CustomerType;
use App\Form\Type\Order\OrderItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $products = $options['products'] ?? [];

        $builder
            ->add('customer', CustomerType::class)
            ->add('category', CategoryType::class)
            ->add('orderItems', CollectionType::class, [
                'entry_type' => OrderItemType::class,
                'entry_options' => [
                    'products' => $products
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'products' => []
        ]);
    }
}
