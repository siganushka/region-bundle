<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Entity\AbstractNestable;
use Siganushka\RegionBundle\Doctrine\ORM\Id\RegionCodeGenerator;
use Siganushka\RegionBundle\Repository\RegionRepository;

/**
 * @extends AbstractNestable<AbstractRegion>
 */
#[ORM\MappedSuperclass(repositoryClass: RegionRepository::class)]
abstract class AbstractRegion extends AbstractNestable
{
    #[ORM\Id]
    #[ORM\Column(length: 9)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: RegionCodeGenerator::class)]
    protected string $code;

    #[ORM\Column]
    protected string $name;

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

    public function getName(): string
    {
        return $this->name;
    }
}
