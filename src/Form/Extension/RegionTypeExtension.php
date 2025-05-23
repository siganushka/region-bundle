<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Form\Extension;

use Siganushka\RegionBundle\Form\Type\RegionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegionTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['cascader_target']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'formModifier']);
            $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'formModifier']);
        }
    }

    /**
     * @param array{ cascader_target: string|null } $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['cascader_target']) {
            $controllerName = 'siganushka-region';

            $view->vars['attr']['data-controller'] = $controllerName;
            $view->vars['attr']['data-action'] = \sprintf('change->%s#change', $controllerName);
            $view->vars['attr']["data-{$controllerName}-url-value"] = $this->urlGenerator->generate('siganushka_region_region_getcollection');
            $view->vars['attr']["data-{$controllerName}-cascader-value"] = \sprintf('%s_%s', $view->parent->vars['id'] ?? '', $options['cascader_target']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('cascader_target', null);
        $resolver->setAllowedTypes('cascader_target', ['null', 'string']);
    }

    public function formModifier(FormEvent $event): void
    {
        $form = $event->getForm();
        $parent = $form->getParent();
        if (!$parent instanceof FormInterface) {
            return;
        }

        /** @var string */
        $targetName = $form->getConfig()->getOption('cascader_target');
        $parentData = $event instanceof PostSubmitEvent
            ? $event->getForm()->getData()
            : $event->getData();

        $target = $parent->get($targetName);
        $targetOptions = $target->getConfig()->getOptions();

        $targetOptions['parent'] = $parentData;
        $targetOptions['root_on_null_parent'] = false;

        // Before remove origin form.
        $parent->remove($targetName);
        $parent->add($targetName, RegionType::class, $targetOptions);
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            RegionType::class,
        ];
    }
}
