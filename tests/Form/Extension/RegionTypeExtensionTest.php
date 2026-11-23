<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Form\Extension;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Siganushka\RegionBundle\Form\Extension\RegionTypeExtension;
use Siganushka\RegionBundle\Form\Type\RegionType;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Siganushka\RegionBundle\Tests\Fixtures\TestRegion;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RegionTypeExtensionTest extends TypeTestCase
{
    private TestRegion $province;
    private TestRegion $city;
    private TestRegion $district;

    /**
     * @var RegionRepository&MockObject
     */
    private RegionRepository $regionRepository;

    /**
     * @var UrlGeneratorInterface&MockObject
     */
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->district = new TestRegion('111000', 'baz');

        $this->city = new TestRegion('110000', 'bar');
        $this->city->addChild($this->district);

        $this->province = new TestRegion('100000', 'foo');
        $this->province->addChild($this->city);

        $this->regionRepository = $this->createMock(RegionRepository::class);
        $this->regionRepository->method('findByParent')->with(null)->willReturn([$this->province]);

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlGenerator->method('generate')
            ->with('siganushka_region_getcollection')
            ->willReturn('/regions')
        ;

        parent::setUp();
    }

    public function testGetExtendedTypes(): void
    {
        static::assertSame([RegionType::class], iterator_to_array(RegionTypeExtension::getExtendedTypes()));
    }

    public function testBuildViewWithCascaderTarget(): void
    {
        $view = $this->createForm(true)->createView();
        $provinceView = $view['province'];

        static::assertSame('siganushka-region', $provinceView->vars['attr']['data-controller']);
        static::assertSame('change->siganushka-region#change', $provinceView->vars['attr']['data-action']);
        static::assertSame('/regions', $provinceView->vars['attr']['data-siganushka-region-url-value']);
        static::assertSame($view->vars['id'].'_city', $provinceView->vars['attr']['data-siganushka-region-cascader-value']);
    }

    public function testBuildViewWithoutCascaderTarget(): void
    {
        $view = $this->createForm(false)->createView();

        static::assertArrayNotHasKey('data-controller', $view['province']->vars['attr']);
    }

    public function testFormModifierOnPreSetData(): void
    {
        $form = $this->createForm(true);

        $form->get('province')->setData($this->province);

        $this->assertCityConfigured($form);
    }

    public function testFormModifierOnPostSubmit(): void
    {
        $form = $this->createForm(true);

        $provinceForm = $form->get('province');
        $provinceForm->setData($this->province);

        $extension = new RegionTypeExtension($this->urlGenerator);
        $extension->formModifier(new PostSubmitEvent($provinceForm, $this->province));

        $this->assertCityConfigured($form);
    }

    private function createForm(bool $cascaderTarget): FormInterface
    {
        $builder = $this->factory->createNamedBuilder('root', FormType::class, null, ['data_class' => null]);
        $builder->add('province', RegionType::class, $cascaderTarget ? ['cascader_target' => 'city'] : []);
        $builder->add('city', RegionType::class);

        return $builder->getForm();
    }

    private function assertCityConfigured(FormInterface $form): void
    {
        $city = $form->get('city');

        static::assertSame($this->province, $city->getConfig()->getOption('parent'));
        static::assertFalse($city->getConfig()->getOption('root_on_null_parent'));

        $choices = $city->getConfig()->getOption('choices');
        static::assertInstanceOf(Collection::class, $choices);
        static::assertSame([$this->city], $choices->toArray());
    }

    protected function getTypes(): array
    {
        return [
            new RegionType($this->regionRepository),
        ];
    }

    protected function getTypeExtensions(): array
    {
        return [
            new RegionTypeExtension($this->urlGenerator),
        ];
    }
}
