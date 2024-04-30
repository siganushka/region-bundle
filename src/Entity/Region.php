<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\RegionBundle\IdGenerator\RegionIdGenerator;
use Siganushka\RegionBundle\Repository\RegionRepository;

/**
 * @ORM\Entity(repositoryClass=RegionRepository::class)
 */
class Region implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=RegionIdGenerator::class)
     * @ORM\Column(type="string")
     */
    private ?string $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="children", cascade={"all"})
     */
    private ?Region $parent = null;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"parent": "ASC", "id": "ASC"})
     *
     * @var Collection<int, Region>
     */
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
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

    public function getCode(): ?string
    {
        return $this->id;
    }

    public function setCode(string $code): self
    {
        $this->id = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
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
