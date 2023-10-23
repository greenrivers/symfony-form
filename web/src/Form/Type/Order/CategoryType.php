<?php

declare(strict_types=1);

namespace App\Form\Type\Order;

use App\Entity\Category;
use App\Form\DataMapper\CategoryDataMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function __construct(private readonly CategoryDataMapper $categoryDataMapper)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category'
            ])
            ->setDataMapper($this->categoryDataMapper);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }
}
