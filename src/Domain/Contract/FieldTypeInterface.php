<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Contract;

use Letkode\FormSchemaBundle\Domain\ValueObject\FieldAttributes;

interface FieldTypeInterface
{
    public static function getName(): string;

    public function takesOptions(): bool;

    public function getDefaultAttributes(): FieldAttributes;

    public function formatDefaultValue(mixed $rawValue): mixed;

    public function getDefaultParams(): array;
}
