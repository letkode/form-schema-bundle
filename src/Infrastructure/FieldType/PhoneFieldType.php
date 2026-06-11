<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;
use Letkode\FormSchemaBundle\Domain\ValueObject\ValidationRules;

#[AsFieldType]
final class PhoneFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'phone';
    }

    #[\Override]
    public function getDefaultValidationRules(): ValidationRules
    {
        return new ValidationRules(maxLength: 30);
    }
}
