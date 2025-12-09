<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Tests\Entity;

use Siganushka\RegionBundle\Entity\Region;
use Siganushka\RegionBundle\Repository\RegionRepository;

trait RegionTestTrait
{
    protected RegionRepository $regionRepository;
    protected Region $province;
    protected Region $city;
    protected Region $district;

    protected function setUp(): void
    {
        $district = new Region('111000', 'baz');

        $city = new Region('110000', 'bar');
        $city->addChild($district);

        $province = new Region('100000', 'foo');
        $province->addChild($city);

        $regionRepository = $this->createMock(RegionRepository::class);

        $regionRepository->expects(static::any())
            ->method('findByParent')
            ->willReturnCallback(function (?string $parent) use ($province, $city, $district): array {
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
            ->willReturnCallback(function (string $code) use ($province, $city, $district): ?Region {
                if ('100000' === $code) {
                    return $province;
                } elseif ('110000' === $code) {
                    return $city;
                } elseif ('111000' === $code) {
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
}
