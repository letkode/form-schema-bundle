<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Contract;

use Letkode\FormSchemaBundle\Domain\ValueObject\FieldAttributes;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

interface FieldTypeInterface
{
    public static function getName(): string;

    public function takesOptions(): bool;

    public function getDefaultAttributes(): FieldAttributes;

    public function getDefaultValidationRules(): ValidationRules;

    public function formatDefaultValue(mixed $rawValue): mixed;

    /** @return array<string, mixed> */
    public function getDefaultParams(): array;
}
