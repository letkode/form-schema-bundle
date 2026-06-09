<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class SwitchFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'switch';
    }

    #[\Override]
    public function takesOptions(): bool
    {
        return true;
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        if (null === $rawValue) {
            return null;
        }

        return \in_array($rawValue, [true, 1, '1', 'true', 'yes', 'si', 'SI', 'YES', 'TRUE'], true);
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'color' => 'primary',
            'variant' => 'solid',
            'layout' => 'vertical',
        ];
    }
}
