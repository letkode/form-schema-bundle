<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\InteractionHandler;

use Letkode\FormSchemaBundle\Domain\Contract\InteractionHandlerInterface;

abstract class AbstractInteractionHandler implements InteractionHandlerInterface
{
    #[\Override]
    public function getDefaultParams(): array
    {
        return [];
    }
}
