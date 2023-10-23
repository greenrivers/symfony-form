<?php

declare(strict_types=1);

namespace App\Form\DataMapper;

use App\Entity\Category;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Traversable;

class CategoryDataMapper implements DataMapperInterface
{
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $forms['name']->setData($viewData);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $viewData = $forms['name']->getData();
    }
}
