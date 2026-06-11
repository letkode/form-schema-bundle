<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Domain\Contract\FieldTypeInterface;
use Letkode\FormSchemaBundle\Domain\ValueObject\FieldAttributes;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

abstract class AbstractFieldType implements FieldTypeInterface
{
    #[\Override]
    public function takesOptions(): bool
    {
        return false;
    }

    #[\Override]
    public function getDefaultAttributes(): FieldAttributes
    {
        return new FieldAttributes(validation: $this->getDefaultValidationRules());
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        return ValidationRules::empty();
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        return $rawValue;
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'label_style' => 'default',
        ];
    }
}
