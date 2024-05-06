<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;

abstract class AbstractRegionTest extends TestCase
{
    protected ?RegionRepository $regionRepository = null;
    protected ?Region $province = null;
    protected ?Region $city = null;
    protected ?Region $district = null;

    protected function setUp(): void
    {
        $district = new Region('111000', 'baz');

        $city = new Region('110000', 'bar');
        $city->addChild($district);

        $province = new Region('100000', 'foo');
        $province->addChild($city);

        $regionRepository = $this->createMock(RegionRepository::class);

        $regionRepository->expects(static::any())
            ->method('findBy')
            ->willReturnCallback(function (array $criteria) use ($province, $city, $district): array {
                $parent = $criteria['parent'] ?? null;
                if (null === $parent) {
                    return [$province];
                } elseif ('100000' === $parent) {
                    return [$city];
                } elseif ('110000' === $parent) {
                    return [$district];
                } else {
                    return [];
                }
            })
        ;

        $regionRepository->expects(static::any())
            ->method('find')
            ->willReturnCallback(function (string $id) use ($province, $city, $district): ?Region {
                if ('100000' === $id) {
                    return $province;
                } elseif ('110000' === $id) {
                    return $city;
                } elseif ('111000' === $id) {
                    return $district;
                } else {
                    return null;
                }
            })
        ;

        $this->regionRepository = $regionRepository;
        $this->province = $province;
        $this->city = $city;
        $this->district = $district;
    }

    protected function tearDown(): void
    {
        $this->regionRepository = null;
        $this->province = null;
        $this->city = null;
        $this->district = null;
    }
}
