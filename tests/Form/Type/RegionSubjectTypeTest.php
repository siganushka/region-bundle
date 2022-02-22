<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Siganushka\RegionBundle\Form\Type\RegionSubjectType;

final class RegionSubjectTypeTest extends AbstractRegionTypeTest
{
    public function testRegionSubjectType(): void
    {
        $form = $this->createFormBuilder()
            ->add('foo', RegionSubjectType::class)
            ->getForm()
        ;

        static::assertTrue($form['foo']->has('province'));
        static::assertTrue($form['foo']->has('city'));
        static::assertTrue($form['foo']->has('district'));

        static::assertSame([], $form['foo']->getConfig()->getOption('province_options'));
        static::assertSame([], $form['foo']->getConfig()->getOption('city_options'));
        static::assertSame([], $form['foo']->getConfig()->getOption('district_options'));

        static::assertSame([$this->province], $form['foo']['province']->getConfig()->getOption('choices'));
        static::assertSame([], $form['foo']['city']->getConfig()->getOption('choices'));
        static::assertSame([], $form['foo']['district']->getConfig()->getOption('choices'));

        static::assertTrue($form['foo']->getConfig()->getOption('inherit_data'));

        static::assertNull($form->getData());
        static::assertFalse($form->isSubmitted());

        $form->submit(['foo' => ['province' => '100000', 'city' => '200000', 'district' => '300000']]);

        $data = [
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
        ];

        static::assertSame([$data['province']], $form['foo']['province']->getConfig()->getOption('choices'));
        static::assertSame([$data['city']], $form['foo']['city']->getConfig()->getOption('choices'));
        static::assertSame([$data['district']], $form['foo']['district']->getConfig()->getOption('choices'));

        static::assertSame($data, $form->getData());
        static::assertTrue($form->isSubmitted());
    }

    public function testRegionSubjectTypeByRoot(): void
    {
        $form = $this->createFormBuilder(RegionSubjectType::class)
            ->getForm()
        ;

        static::assertTrue($form->has('province'));
        static::assertFalse($form->has('city'));
        static::assertFalse($form->has('district'));
    }
}
