<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Contract;

interface OptionGeneralSeederInterface
{
    /** @return array<string, mixed> */
    public function getOptionGeneralData(): array;
}
