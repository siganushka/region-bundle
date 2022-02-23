<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface RegionInterface
{
    public function getParent(): ?self;

    public function setParent(?self $parent): self;

    public function getCode(): ?string;

    public function setCode(string $code): self;

    public function getName(): ?string;

    public function setName(string $name): self;

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection;

    /**
     * @return array<int, self>
     */
    public function getAncestors(bool $includeSelf = false): array;

    /**
     * @return array<int, self>
     */
    public function getSiblings(bool $includeSelf = false): array;

    /**
     * @return array<int, self>
     */
    public function getDescendants(bool $includeSelf = false): array;

    public function isRoot(): bool;

    public function isLeaf(): bool;

    public function getDepth(): int;
}
