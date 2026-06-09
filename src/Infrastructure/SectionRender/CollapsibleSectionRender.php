<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\SectionRender;

use Letkode\FormSchemaBundle\Attribute\AsSectionRender;
use Letkode\FormSchemaBundle\Domain\Contract\SectionRenderInterface;

#[AsSectionRender]
final class CollapsibleSectionRender implements SectionRenderInterface
{
    #[\Override]
    public static function getName(): string
    {
        return 'collapsible';
    }

    #[\Override]
    public function renderMeta(array $parameters): array
    {
        return [
            'default_collapsed' => $parameters['collapsible']['default_collapsed'] ?? false,
        ];
    }
}
