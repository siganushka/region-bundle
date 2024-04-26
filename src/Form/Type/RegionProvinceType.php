<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Form\Type;

use Siganushka\RegionBundle\Entity\RegionInterface;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionProvinceType extends AbstractType
{
    private RegionRepository $regionRepository;

    public function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formModifier = function (?FormInterface $form, ?RegionInterface $parent) use ($options): void {
            if (null === $form) {
                return;
            }

            $form->add('city', RegionCityType::class, array_merge([
                'parent' => $parent,
                'district_options' => $options['district_options'],
            ], $options['city_options']));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier): void {
            $form = $event->getForm()->getParent();
            /** @var RegionInterface */
            $data = $event->getData();

            $formModifier($form, $data);
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier): void {
            $form = $event->getForm()->getParent();
            /** @var RegionInterface */
            $data = $event->getForm()->getData();

            $formModifier($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choices = $this->regionRepository->findBy(['parent' => null], ['parent' => 'ASC', 'id' => 'ASC']);

        $resolver->setDefaults([
            'choices' => $choices,
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'city_options' => [],
            'district_options' => [],
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
