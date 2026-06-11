<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

#[AsFieldType]
final class EmailFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'email';
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        return new ValidationRules(maxLength: 254);
    }
}
