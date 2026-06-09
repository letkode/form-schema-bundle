<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Exception;

final class UnknownContextException extends \RuntimeException
{
    public function __construct(string $context)
    {
        parent::__construct(\sprintf('Unknown context "%s" — no field attribute with this key was found.', $context));
    }
}
