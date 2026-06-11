<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\InteractionHandler;

use Letkode\FormSchemaBundle\Attribute\AsInteractionHandler;

#[AsInteractionHandler]
final class AjaxValidateInteractionHandler extends AbstractInteractionHandler
{
    #[\Override]
    public static function getName(): string
    {
        return 'ajax_validate';
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function getDefaultParams(): array
    {
        return [
            'method' => 'GET',
            'debounce' => 0,
        ];
    }
}
