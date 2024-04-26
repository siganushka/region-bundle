<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionProvinceType;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;

abstract class AbstractRegionTypeTest extends AbstractRegionTest
{
    /**
     * @param mixed $data
     */
    protected function createFormBuilder(string $type = FormType::class, $data = null, array $options = []): FormBuilderInterface
    {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType(new RegionProvinceType($this->regionRepository));

        return $formFactoryBuilder->getFormFactory()
            ->createBuilder($type, $data, $options)
        ;
    }
}
