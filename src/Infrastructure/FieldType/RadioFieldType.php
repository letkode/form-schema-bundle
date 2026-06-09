<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class RadioFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'radio';
    }

    #[\Override]
    public function takesOptions(): bool
    {
        return true;
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'color' => 'primary',
            'variant' => 'default',
            'layout' => 'vertical',
        ];
    }
}
