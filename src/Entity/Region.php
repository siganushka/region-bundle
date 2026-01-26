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
    protected string $fullname;

    #[ORM\Column]
    protected int $level;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;

        parent::__construct();
    }

    public function setParent(?Nestable $parent): static
    {
        parent::setParent($parent);
        unset($this->fullname, $this->level);

        return $this;
    }

    public function addChild(Nestable $child): static
    {
        parent::addChild($child);
        unset($this->fullname, $this->level);

        return $this;
    }

    public function removeChild(Nestable $child): static
    {
        parent::removeChild($child);
        unset($this->fullname, $this->level);

        return $this;
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

    #[ORM\PrePersist]
    public function getFullname(): string
    {
        if (isset($this->fullname)) {
            return $this->fullname;
        }

        $callback = static fn (Region $item) => $item->getName();
        $ancestorNames = array_map($callback, $this->getAncestors(true));

        return $this->fullname = implode('/', $ancestorNames);
    }

    public function setFullname(string $fullname): static
    {
        throw new \BadMethodCallException('The fullname cannot be modified anymore.');
    }

    #[ORM\PrePersist]
    public function getLevel(): int
    {
        if (isset($this->level)) {
            return $this->level;
        }

        return $this->level = $this->getDepth() + 1;
    }

    public function setLevel(int $level): static
    {
        throw new \BadMethodCallException('The level cannot be modified anymore.');
    }
}
