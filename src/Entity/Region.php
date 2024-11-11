<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\RegionBundle\Doctrine\ORM\Id\RegionCodeGenerator;
use Siganushka\RegionBundle\Repository\RegionRepository;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: RegionCodeGenerator::class)]
    protected string $id;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children', cascade: ['all'])]
    protected ?Region $parent = null;

    #[ORM\Column]
    protected string $name;

    /** @var Collection<int, Region> */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['all'])]
    #[ORM\OrderBy(['parent' => 'ASC', 'id' => 'ASC'])]
    protected Collection $children;

    public function __construct(string $code, string $name)
    {
        $this->id = $code;
        $this->name = $name;
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        if ($parent && $parent === $this) {
            throw new \InvalidArgumentException('The parent conflict has been detected.');
        }

        if ($parent && \in_array($parent, $this->getDescendants(), true)) {
            throw new \InvalidArgumentException('The descendants conflict has been detected.');
        }

        $this->parent = $parent;

        return $this;
    }

    public function getCode(): string
    {
        return $this->id;
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

    /**
     * @return Collection<int, Region>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getAncestors(bool $includeSelf = false): array
    {
        $parents = $includeSelf ? [$this] : [];
        $node = $this;

        while ($parent = $node->getParent()) {
            array_unshift($parents, $parent);
            $node = $parent;
        }

        return $parents;
    }

    public function getSiblings(bool $includeSelf = false): array
    {
        if (null === $this->parent) {
            return [];
        }

        $siblings = [];
        foreach ($this->parent->getChildren() as $child) {
            if ($includeSelf || $child !== $this) {
                $siblings[] = $child;
            }
        }

        return $siblings;
    }

    public function getDescendants(bool $includeSelf = false): array
    {
        $descendants = $includeSelf ? [$this] : [];

        foreach ($this->children as $child) {
            $descendants[] = $child;
            if (!$child->isLeaf()) {
                $descendants = array_merge($descendants, $child->getDescendants());
            }
        }

        return $descendants;
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function isLeaf(): bool
    {
        return 0 === \count($this->children);
    }

    public function getDepth(): int
    {
        if (null === $this->parent) {
            return 0;
        }

        return $this->parent->getDepth() + 1;
    }
}
