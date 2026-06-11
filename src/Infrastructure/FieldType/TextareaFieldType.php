<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

#[AsFieldType]
final class TextareaFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'textarea';
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        return new ValidationRules(maxLength: 5000);
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'label_style' => 'default',
            'icon_leading' => null,
            'icon_trailing' => null,
        ];
    }
}
