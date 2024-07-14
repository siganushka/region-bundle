<?php

declare(strict_types=1);

namespace Siganushka\RegionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SiganushkaRegionBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
