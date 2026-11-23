<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Type;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Siganushka\RegionBundle\Form\Type\RegionType;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class RegionTypeTest extends TypeTestCase
{
    private TestRegion $province;
    private TestRegion $city;
    private TestRegion $district;

    /**
     * @var RegionRepository&MockObject
     */
    private RegionRepository $regionRepository;

    protected function setUp(): void
    {
        $this->district = new TestRegion('111000', 'baz');

        $this->city = new TestRegion('110000', 'bar');
        $this->city->addChild($this->district);

        $this->province = new TestRegion('100000', 'foo');
        $this->province->addChild($this->city);

        $this->regionRepository = $this->createMock(RegionRepository::class);

        parent::setUp();
    }

    public function testGetParent(): void
    {
        static::assertSame(ChoiceType::class, (new RegionType($this->regionRepository))->getParent());
    }

    public function testDefaultOptions(): void
    {
        $this->regionRepository->expects(static::once())
            ->method('findByParent')
            ->with(null)
            ->willReturn([$this->province])
        ;

        $form = $this->factory->create(RegionType::class);

        static::assertSame('code', $form->getConfig()->getOption('choice_value'));
        static::assertSame('name', $form->getConfig()->getOption('choice_label'));
        static::assertFalse($form->getConfig()->getOption('choice_translation_domain'));
        static::assertNull($form->getConfig()->getOption('parent'));
        static::assertTrue($form->getConfig()->getOption('root_on_null_parent'));
        static::assertSame([$this->province], $form->getConfig()->getOption('choices'));
    }

    public function testChoicesFromParent(): void
    {
        $this->regionRepository->expects(static::never())->method('findByParent');

        $form = $this->factory->create(RegionType::class, null, ['parent' => $this->city]);

        static::assertSame($this->city, $form->getConfig()->getOption('parent'));
        static::assertFalse($form->getConfig()->getOption('root_on_null_parent'));
        $choices = $form->getConfig()->getOption('choices');
        static::assertInstanceOf(Collection::class, $choices);
        static::assertSame([$this->district], $choices->toArray());
    }

    public function testChoicesWithoutRootOnNullParent(): void
    {
        $this->regionRepository->expects(static::never())->method('findByParent');

        $form = $this->factory->create(RegionType::class, null, ['root_on_null_parent' => false]);

        static::assertFalse($form->getConfig()->getOption('root_on_null_parent'));
        static::assertSame([], $form->getConfig()->getOption('choices'));
    }

    public function testInvalidParentOption(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(RegionType::class, null, ['parent' => new \stdClass()]);
    }

    protected function getTypes(): array
    {
        return [
            new RegionType($this->regionRepository),
        ];
    }
}
