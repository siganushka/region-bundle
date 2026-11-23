<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Dto;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Dto\RegionQueryDto;

final class RegionQueryDtoTest extends TestCase
{
    public function testShouldQueryParent(): void
    {
        static::assertTrue(RegionQueryDto::shouldQueryParent(new RegionQueryDto('100000', null)));
        static::assertTrue(RegionQueryDto::shouldQueryParent(new RegionQueryDto(null, null)));
        static::assertFalse(RegionQueryDto::shouldQueryParent(new RegionQueryDto(null, 'foo')));
    }
}
