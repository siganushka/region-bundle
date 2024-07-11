<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Form\Type;

use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    public function __construct(private readonly RegionRepository $regionRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $choicesNormalizer = function (Options $options): iterable {
            /** @var Region|null */
            $parent = $options['parent'];
            if ($options['root_on_null_parent'] && !$parent) {
                return $this->regionRepository->findBy(['parent' => null]);
            }

            return $parent ? $parent->getChildren() : [];
        };

        $rootOnNullParentNormalizer = function (Options $options, bool $rootOnNullParent): bool {
            if ($options['parent']) {
                return false;
            }

            return $rootOnNullParent;
        };

        $resolver->setDefaults([
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'parent' => null,
            // Show root regions on null parent.
            'root_on_null_parent' => true,
        ]);

        $resolver->setAllowedTypes('parent', ['null', Region::class]);
        $resolver->setAllowedTypes('root_on_null_parent', 'bool');

        $resolver->setNormalizer('choices', $choicesNormalizer);
        $resolver->setNormalizer('root_on_null_parent', $rootOnNullParentNormalizer);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
