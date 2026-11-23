<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\SiganushkaRegionBundle;

final class SiganushkaRegionBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        static::assertSame(\dirname(__DIR__), (new SiganushkaRegionBundle())->getPath());
    }
}
