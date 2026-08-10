<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Dto;

class RegionQueryDto
{
    public function __construct(
        public readonly ?string $parent,
        public readonly ?string $name)
    {
    }
}
