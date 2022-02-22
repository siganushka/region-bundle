<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionDistrictType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class RegionDistrictTypeTest extends AbstractRegionTypeTest
{
    public function testRegionDistrictType(): void
    {
        $form = $this->createFormBuilder(RegionDistrictType::class)
            ->getForm()
        ;

        static::assertSame([], $form->getConfig()->getOption('choices'));
        static::assertNull($form->getConfig()->getOption('parent'));
        static::assertFalse($form->getConfig()->getOption('choice_translation_domain'));
    }

    public function testRegionDistrictTypeWithOptions(): void
    {
        $options = [
            'parent' => $this->city,
        ];

        $form = $this->createFormBuilder(RegionDistrictType::class, null, $options)
            ->getForm()
        ;

        static::assertSame([$this->district], $form->getConfig()->getOption('choices'));
        static::assertSame($this->city, $form->getConfig()->getOption('parent'));

        static::assertNull($form->getData());
        static::assertFalse($form->isSubmitted());

        $form->submit('300000');

        static::assertSame($this->district, $form->getData());
        static::assertTrue($form->isSubmitted());
    }

    public function testRegionDistrictTypeParentException(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->createFormBuilder(RegionDistrictType::class, null, [
            'parent' => new \stdClass(),
        ]);
    }
}
