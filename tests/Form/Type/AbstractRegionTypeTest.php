<?php

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionProvinceType;
use Siganushka\RegionBundle\Tests\Entity\AbstractRegionTest;
use Symfony\Component\Form\FormFactoryBuilder;

abstract class AbstractRegionTypeTest extends AbstractRegionTest
{
    protected function createFormBuilder(string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = [])
    {
        $formFactoryBuilder = new FormFactoryBuilder();
        $formFactoryBuilder->addType(new RegionProvinceType($this->managerRegistry));

        $formBuilder = $formFactoryBuilder->getFormFactory()
            ->createBuilder($type, $data, $options);

        return $formBuilder;
    }
}
