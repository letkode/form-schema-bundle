<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class NumberFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'number';
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'label_style' => 'default',
            'min' => null,
            'max' => null,
            'step' => 1,
            'button_layout' => 'default',
        ];
    }
}
