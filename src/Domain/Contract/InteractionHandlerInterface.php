<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Contract;

interface InteractionHandlerInterface
{
    public static function getName(): string;

    /** @return array<string, mixed> */
    public function getDefaultParams(): array;
}
