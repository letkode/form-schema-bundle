<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Domain\Contract\FieldTypeInterface;
use Letkode\FormSchemaBundle\Domain\ValueObject\FieldAttributes;

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
        return FieldAttributes::default();
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        return $rawValue;
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'label_style' => 'default',
        ];
    }
}
