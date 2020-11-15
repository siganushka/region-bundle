<?php

namespace Siganushka\RegionBundle\Tests\Serializer\Normalizer;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Serializer\Normalizer\RegionNormalizer;

class RegionNormalizerTest extends TestCase
{
    public function testNormalize()
    {
        $region = new Region();
        $region->setCode('100000');
        $region->setName('foo');

        $normalizer = new RegionNormalizer();

        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));

        $this->assertTrue($normalizer->supportsNormalization($region));
        $this->assertSame(['code' => '100000', 'name' => 'foo'], $normalizer->normalize($region));
    }
}
