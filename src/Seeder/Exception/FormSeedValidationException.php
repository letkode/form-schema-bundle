<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Exception;

final class FormSeedValidationException extends \RuntimeException
{
    /** @param list<string> $errors */
    public function __construct(
        private readonly array $errors,
        string $context = '',
    ) {
        $prefix  = $context !== '' ? "[{$context}] " : '';
        $message = $prefix . 'Seed validation failed: ' . implode('; ', $errors);

        parent::__construct($message);
    }

    /** @return list<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
