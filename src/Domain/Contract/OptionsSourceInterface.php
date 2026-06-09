<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Contract;

use Letkode\FormSchemaBundle\Application\DTO\OptionDTO;

interface OptionsSourceInterface
{
    public static function getName(): string;

    /**
     * @param array<string,mixed> $parameters
     *
     * @return list<OptionDTO>
     */
    public function resolve(array $parameters, string|null $locale = null): array;
}
