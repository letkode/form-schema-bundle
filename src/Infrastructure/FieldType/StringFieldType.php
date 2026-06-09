<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class StringFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'string';
    }
}
