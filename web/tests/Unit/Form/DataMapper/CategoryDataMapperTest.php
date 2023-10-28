<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\DataMapper;

use App\Entity\Category;
use App\Form\DataMapper\CategoryDataMapper;
use PHPUnit\Framework\TestCase;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Form\FormInterface;

class CategoryDataMapperTest extends TestCase
{
    private CategoryDataMapper $categoryDataMapper;

    protected function setUp(): void
    {
        $this->categoryDataMapper = new CategoryDataMapper();
    }

    /**
     * @covers CategoryDataMapper::mapFormsToData
     */
    public function testMapFormsToData(): void
    {
        $viewData = null;
        $category = new Category();
        $category->setName('Test category');

        $form = $this->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $form->expects(self::once())
            ->method('getData')
            ->willReturn($category);

        $arrayIterator = new RecursiveArrayIterator(['name' => $form]);
        $forms = new RecursiveIteratorIterator($arrayIterator, RecursiveIteratorIterator::SELF_FIRST);

        $this->categoryDataMapper->mapFormsToData($forms, $viewData);

        $this->assertEquals($category, $viewData);
    }
}
