<?php

declare(strict_types=1);

namespace App\Form\Type\Order;

use App\Entity\OrderItem;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $products = $options['products'] ?? [];

        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choices' => $products,
                'choice_label' => fn(Product $product) => $product->getName() . ' => ' . $product->getPrice(),
                'label' => 'order_item.product',
                'attr' => [
                    'class' => 'order-item-product'
                ]
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'order_item.quantity'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
            'products' => []
        ]);
    }
}
