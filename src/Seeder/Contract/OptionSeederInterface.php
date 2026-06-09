<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Contract;

interface OptionSeederInterface
{
    /** @return array<string, mixed> */
    public function getOptionData(): array;
}
