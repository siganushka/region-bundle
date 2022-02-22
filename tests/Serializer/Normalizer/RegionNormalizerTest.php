<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Serializer\Normalizer;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Serializer\Normalizer\RegionNormalizer;

final class RegionNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $region = new Region();
        $region->setCode('100000');
        $region->setName('foo');

        $normalizer = new RegionNormalizer();

        static::assertFalse($normalizer->supportsNormalization(new \stdClass()));

        static::assertTrue($normalizer->supportsNormalization($region));
        static::assertSame(['code' => '100000', 'name' => 'foo'], $normalizer->normalize($region));
    }
}
