<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Contract;

use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

interface SeedLoaderInterface
{
    /** @return list<SeedSource> */
    public function load(string|null $filter = null): array;
}
