<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

interface RegionInterface
{
    public function getCode(): ?string;

    public function setCode(string $code): self;

    public function getName(): ?string;

    public function setName(string $name): self;
}
