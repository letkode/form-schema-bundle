<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\InteractionHandler;

use Letkode\FormSchemaBundle\Attribute\AsInteractionHandler;

#[AsInteractionHandler]
final class ComputeInteractionHandler extends AbstractInteractionHandler
{
    #[\Override]
    public static function getName(): string
    {
        return 'compute';
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'decimals' => null,
        ];
    }
}
