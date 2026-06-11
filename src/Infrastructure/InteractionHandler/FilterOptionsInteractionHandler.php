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

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'mode' => 'server',
        ];
    }
}
