<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class SelectFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'select';
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
            'searchable' => false,
            'tags_mode' => false,
            'search_limit' => null,
            'min_search_length' => null,
        ];
    }
}
