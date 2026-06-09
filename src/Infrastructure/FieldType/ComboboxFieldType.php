<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class ComboboxFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'combobox';
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'open_on_focus' => false,
            'min_search_length' => 0,
            'api_url' => null,
        ];
    }
}
