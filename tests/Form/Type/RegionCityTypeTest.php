<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionCityType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class RegionCityTypeTest extends AbstractRegionTypeTest
{
    public function testRegionCityType(): void
    {
        $form = $this->createFormBuilder(RegionCityType::class)
            ->getForm()
        ;

        static::assertSame([], $form->getConfig()->getOption('choices'));
        static::assertSame([], $form->getConfig()->getOption('district_options'));
        static::assertNull($form->getConfig()->getOption('parent'));
    }

    public function testRegionCityTypeWithOptions(): void
    {
        $options = [
            'parent' => $this->province,
            'district_options' => ['placeholder' => 'baz'],
        ];

        $form = $this->createFormBuilder(RegionCityType::class, null, $options)
            ->getForm()
        ;

        static::assertSame([$this->city], $form->getConfig()->getOption('choices'));
        static::assertSame($options['district_options'], $form->getConfig()->getOption('district_options'));
        static::assertSame($this->province, $form->getConfig()->getOption('parent'));
        static::assertFalse($form->getConfig()->getOption('choice_translation_domain'));

        static::assertNull($form->getData());
        static::assertFalse($form->isSubmitted());

        $form->submit('200000');

        static::assertSame($this->city, $form->getData());
        static::assertTrue($form->isSubmitted());
    }

    public function testRegionCityTypeParentException(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFormBuilder(RegionCityType::class, null, [
            'parent' => new \stdClass(),
        ]);
    }
}
