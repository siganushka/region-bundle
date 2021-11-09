<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Form\Type;

use Siganushka\RegionBundle\Entity\RegionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formModifier = function (?FormInterface $form, RegionInterface $parent = null) use ($options): void {
            if (null === $form) {
                return;
            }

            $form->add('district', RegionDistrictType::class, array_merge([
                'parent' => $parent,
            ], $options['district_options']));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier): void {
            $form = $event->getForm()->getParent();
            $data = $event->getData();

            $formModifier($form, $data);
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier): void {
            $form = $event->getForm()->getParent();
            $data = $event->getForm()->getData();

            $formModifier($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'district_options' => [],
            'parent' => null,
        ]);

        $resolver->setAllowedTypes('parent', ['null', RegionInterface::class]);

        $resolver->setNormalizer('choices', function (Options $options) {
            return $options['parent'] ? $options['parent']->getChildren()->toArray() : [];
        });
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
