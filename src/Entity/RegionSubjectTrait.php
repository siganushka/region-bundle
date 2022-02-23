<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait RegionSubjectTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\RegionBundle\Entity\Region")
     *
     * @Groups({"region_subject"})
     */
    private $province;

    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\RegionBundle\Entity\Region")
     *
     * @Groups({"region_subject"})
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="Siganushka\RegionBundle\Entity\Region")
     *
     * @Groups({"region_subject"})
     */
    private $district;

    public function getProvince(): ?RegionInterface
    {
        return $this->province;
    }

    public function setProvince(?RegionInterface $province): RegionSubjectInterface
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?RegionInterface
    {
        return $this->city;
    }

    public function setCity(?RegionInterface $city): RegionSubjectInterface
    {
        $this->city = $city;

        return $this;
    }

    public function getDistrict(): ?RegionInterface
    {
        return $this->district;
    }

    public function setDistrict(?RegionInterface $district): RegionSubjectInterface
    {
        $this->district = $district;

        return $this;
    }

    public function getRegionAsString(): string
    {
        $names = array_map(function (RegionInterface $region) {
            return $region->getName();
        }, array_filter([$this->province, $this->city, $this->district]));

        return implode('/', $names);
    }
}
