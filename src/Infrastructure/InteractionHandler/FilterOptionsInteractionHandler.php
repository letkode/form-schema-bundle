<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\InteractionHandler;

use Letkode\FormSchemaBundle\Attribute\AsInteractionHandler;

#[AsInteractionHandler]
final class FilterOptionsInteractionHandler extends AbstractInteractionHandler
{
    #[\Override]
    public static function getName(): string
    {
        return 'filter_options';
    }

    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'mode' => 'server',
        ];
    }
}
