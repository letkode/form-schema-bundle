<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class RatingFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'rating';
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
            'max_stars' => 5,
            'half_star' => false,
        ];
    }
}
