<?php

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionDistrictType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class RegionDistrictTypeTest extends AbstractRegionTypeTest
{
    public function testRegionDistrictType()
    {
        $form = $this->createFormBuilder(RegionDistrictType::class)
            ->getForm();

        $this->assertSame([], $form->getConfig()->getOption('choices'));
        $this->assertNull($form->getConfig()->getOption('parent'));
        $this->assertFalse($form->getConfig()->getOption('choice_translation_domain'));
    }

    public function testRegionDistrictTypeWithOptions()
    {
        $options = [
            'parent' => $this->city,
        ];

        $form = $this->createFormBuilder(RegionDistrictType::class, null, $options)
            ->getForm();

        $this->assertSame([$this->district], $form->getConfig()->getOption('choices'));
        $this->assertSame($this->city, $form->getConfig()->getOption('parent'));

        $this->assertNull($form->getData());
        $this->assertFalse($form->isSubmitted());

        $form->submit('300000');

        $this->assertSame($this->district, $form->getData());
        $this->assertTrue($form->isSubmitted());
    }

    public function testRegionDistrictTypeParentException()
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFormBuilder(RegionDistrictType::class, null, [
            'parent' => new \stdClass(),
        ]);
    }
}
