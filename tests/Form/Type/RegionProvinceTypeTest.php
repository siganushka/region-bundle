<?php

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionProvinceType;

class RegionProvinceTypeTest extends AbstractRegionTypeTest
{
    public function testRegionProvinceType()
    {
        $form = $this->createFormBuilder(RegionProvinceType::class)
            ->getForm();

        $this->assertSame([$this->province], $form->getConfig()->getOption('choices'));
        $this->assertSame([], $form->getConfig()->getOption('city_options'));
        $this->assertSame([], $form->getConfig()->getOption('district_options'));
        $this->assertFalse($form->getConfig()->getOption('choice_translation_domain'));

        $this->assertNull($form->getData());
        $this->assertFalse($form->isSubmitted());

        $form->submit('100000');

        $this->assertSame($this->province, $form->getData());
        $this->assertTrue($form->isSubmitted());
    }

    public function testRegionProvinceTypeWithOptions()
    {
        $options = [
            'city_options' => ['placeholder' => 'bar'],
            'district_options' => ['placeholder' => 'baz'],
        ];

        $form = $this->createFormBuilder(RegionProvinceType::class, null, $options)
            ->getForm();

        $this->assertSame($options['city_options'], $form->getConfig()->getOption('city_options'));
        $this->assertSame($options['district_options'], $form->getConfig()->getOption('district_options'));
    }
}
