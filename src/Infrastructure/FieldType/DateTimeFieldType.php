<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Attribute\AsFieldType;

#[AsFieldType]
final class DateTimeFieldType extends AbstractFieldType
{
    #[\Override]
    public static function getName(): string
    {
        return 'datetime';
    }

    #[\Override]
    public function formatDefaultValue(mixed $rawValue): mixed
    {
        if (null === $rawValue || '' === $rawValue) {
            return null;
        }

        if (\is_string($rawValue) && (str_starts_with($rawValue, '+') || str_starts_with($rawValue, '-'))) {
            $date = new \DateTimeImmutable()->modify($rawValue);

            return false !== $date ? $date->format(\DateTimeInterface::ATOM) : $rawValue;
        }

        return $rawValue;
    }
}
