<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Serializer\Normalizer;

use Siganushka\RegionBundle\Entity\RegionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegionNormalizer implements NormalizerInterface
{
    /**
     * @param RegionInterface      $object
     * @param string               $format
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'code' => $object->getCode(),
            'name' => $object->getName(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof RegionInterface;
    }
}
