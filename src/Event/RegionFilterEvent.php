<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Event;

use Siganushka\RegionBundle\Entity\RegionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RegionFilterEvent extends Event
{
    /**
     * @var RegionInterface[]
     */
    private array $regions = [];

    /**
     * @param array<int, RegionInterface>|\Traversable<int, RegionInterface> $regions
     */
    public function __construct(iterable $regions)
    {
        foreach ($regions as $region) {
            if (!$region instanceof RegionInterface) {
                throw new \InvalidArgumentException(sprintf('Array of regions must be type of %s', RegionInterface::class));
            }

            $this->regions[] = $region;
        }
    }

    /**
     * @return RegionInterface[]
     */
    public function getRegions(): array
    {
        return $this->regions;
    }

    /**
     * @param RegionInterface[] $regions
     */
    public function setRegions(array $regions): void
    {
        $this->regions = $regions;
    }
}
