<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\InteractionHandler;

use Letkode\FormSchemaBundle\Attribute\AsInteractionHandler;

#[AsInteractionHandler]
final class SetValueInteractionHandler extends AbstractInteractionHandler
{
    #[\Override]
    public static function getName(): string
    {
        return 'set_value';
    }
}
