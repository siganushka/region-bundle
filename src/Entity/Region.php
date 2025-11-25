<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Entity\Nestable;
use Siganushka\RegionBundle\Doctrine\ORM\Id\RegionCodeGenerator;
use Siganushka\RegionBundle\Repository\RegionRepository;

/**
 * @extends Nestable<Region>
 */
#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Region extends Nestable
{
    #[ORM\Id]
    #[ORM\Column(length: 9)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: RegionCodeGenerator::class)]
    protected string $code;

    #[ORM\Column]
    protected string $name;

    #[ORM\Column]
    protected ?string $fullname = null;

    #[ORM\Column]
    protected ?int $level = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;

        parent::__construct();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        throw new \BadMethodCallException('The code cannot be modified anymore.');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        throw new \BadMethodCallException('The name cannot be modified anymore.');
    }

    public function getFullname(): string
    {
        return $this->fullname ?? $this->calculateFullname();
    }

    public function setFullname(string $fullname): static
    {
        throw new \BadMethodCallException('The fullname cannot be modified anymore.');
    }

    public function getLevel(): int
    {
        return $this->level ?? $this->calculateLevel();
    }

    public function setLevel(int $level): static
    {
        throw new \BadMethodCallException('The level cannot be modified anymore.');
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->fullname = $this->calculateFullname();
        $this->level = $this->calculateLevel();
    }

    private function calculateFullname(): string
    {
        return implode('/', array_map(fn (Region $item) => $item->getName(), $this->getAncestors(true)));
    }

    private function calculateLevel(): int
    {
        return $this->getDepth() + 1;
    }
}
