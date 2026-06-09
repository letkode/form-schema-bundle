<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Contract;

interface FormSeederInterface
{
    /** @return array<string, mixed> */
    public function getFormData(): array;
}
