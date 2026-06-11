<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

#[AsFieldType]
final class PinFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'pin';
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        // minLength = maxLength = default pin length (4). Override via attributes.validation if length differs.
        return new ValidationRules(minLength: 4, maxLength: 4);
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'variant' => 'outline',
            'length' => 4,
            'input_type' => 'text',
            'chars_pattern' => null,
        ];
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        return null !== $rawValue ? (string) $rawValue : null;
    }
}
