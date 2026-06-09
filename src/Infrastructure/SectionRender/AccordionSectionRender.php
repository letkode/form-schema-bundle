<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\SectionRender;

use Letkode\FormSchemaBundle\Attribute\AsSectionRender;
use Letkode\FormSchemaBundle\Domain\Contract\SectionRenderInterface;

#[AsSectionRender]
final class AccordionSectionRender implements SectionRenderInterface
{
    #[\Override]
    public static function getName(): string
    {
        return 'accordion';
    }

    #[\Override]
    public function renderMeta(array $parameters): array
    {
        return [
            'allow_multiple_open' => $parameters['accordion']['allow_multiple_open'] ?? false,
            'first_open' => $parameters['accordion']['first_open'] ?? true,
        ];
    }
}
