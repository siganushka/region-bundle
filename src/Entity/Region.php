<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;

/**
 * @ORM\Entity
 */
class Region implements ResourceInterface, TimestampableInterface, RegionInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="children", cascade={"all"})
     */
    private ?RegionInterface $parent = null;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToMany(targetEntity=Region::class, mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"parent": "ASC", "id": "ASC"})
     *
     * @var Collection<int, RegionInterface>
     */
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getParent(): ?RegionInterface
    {
        return $this->parent;
    }

    public function setParent(?RegionInterface $parent): RegionInterface
    {
        if ($parent && \in_array($parent, $this->getDescendants(), true)) {
            throw new \InvalidArgumentException('The tree node descendants conflict has been detected.');
        }

        $this->parent = $parent;

        return $this;
    }

    public function getCode(): ?string
    {
        if ($this->id) {
            return (string) $this->id;
        }

        return null;
    }

    public function setCode(string $code): RegionInterface
    {
        $this->id = (int) str_pad($code, 6, '0');

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): RegionInterface
    {
        $this->name = mb_substr($name, 0, 32);

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(RegionInterface $child): RegionInterface
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(RegionInterface $child): RegionInterface
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
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
