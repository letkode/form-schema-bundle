<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\GroupRender;

use Letkode\FormSchemaBundle\Attribute\AsGroupRender;
use Letkode\FormSchemaBundle\Domain\Contract\GroupRenderInterface;

#[AsGroupRender]
final class FieldsetGroupRender implements GroupRenderInterface
{
    #[\Override]
    public static function getName(): string
    {
        return 'fieldset';
    }

    #[\Override]
    public function renderMeta(array $parameters): array
    {
        return [
            'legend' => (bool) ($parameters['legend'] ?? true),
            'legend_custom' => $parameters['legend_custom'] ?? null,
        ];
    }
}
