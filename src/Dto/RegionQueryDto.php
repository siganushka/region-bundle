<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Dto;

use Doctrine\Common\Collections\Expr\Comparison;
use Siganushka\GenericBundle\Attribute\QueryFilter;

class RegionQueryDto
{
    public function __construct(
        #[QueryFilter(when: [self::class, 'shouldQueryParent'], forceOnNull: true)]
        public readonly ?string $parent = null,
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $name = null)
    {
    }

    public static function shouldQueryParent(self $dto): bool
    {
        return $dto->parent || !$dto->name;
    }
}
