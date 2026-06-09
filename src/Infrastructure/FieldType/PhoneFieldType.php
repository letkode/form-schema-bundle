<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class PhoneFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'phone';
    }
}
