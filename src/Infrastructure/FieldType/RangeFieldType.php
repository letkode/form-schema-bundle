<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class RangeFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'range';
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'color' => 'primary',
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'show_steps' => false,
        ];
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        return null !== $rawValue ? (int) $rawValue : null;
    }
}
