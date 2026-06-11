<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

#[AsFieldType]
final class PasswordFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'password';
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        return new ValidationRules(minLength: 8);
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'size' => 'md',
            'label_style' => 'default',
            'show_strength' => true,
            'min_length' => 8,
            'checks_exclude' => [],
        ];
    }
}
