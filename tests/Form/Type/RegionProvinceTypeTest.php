<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionProvinceType;

/**
 * @internal
 * @coversNothing
 */
final class RegionProvinceTypeTest extends AbstractRegionTypeTest
{
    public function testRegionProvinceType(): void
    {
        $form = $this->createFormBuilder(RegionProvinceType::class)
            ->getForm()
        ;

        static::assertSame([$this->province], $form->getConfig()->getOption('choices'));
        static::assertSame([], $form->getConfig()->getOption('city_options'));
        static::assertSame([], $form->getConfig()->getOption('district_options'));
        static::assertFalse($form->getConfig()->getOption('choice_translation_domain'));

        static::assertNull($form->getData());
        static::assertFalse($form->isSubmitted());

        $form->submit('100000');

        static::assertSame($this->province, $form->getData());
        static::assertTrue($form->isSubmitted());
    }

    public function testRegionProvinceTypeWithOptions(): void
    {
        $options = [
            'city_options' => ['placeholder' => 'bar'],
            'district_options' => ['placeholder' => 'baz'],
        ];

        $form = $this->createFormBuilder(RegionProvinceType::class, null, $options)
            ->getForm()
        ;

        static::assertSame($options['city_options'], $form->getConfig()->getOption('city_options'));
        static::assertSame($options['district_options'], $form->getConfig()->getOption('district_options'));
    }
}
