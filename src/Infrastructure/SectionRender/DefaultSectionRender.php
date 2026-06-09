<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\SectionRender;

use Letkode\FormSchemaBundle\Attribute\AsSectionRender;
use Letkode\FormSchemaBundle\Domain\Contract\SectionRenderInterface;

#[AsSectionRender]
final class DefaultSectionRender implements SectionRenderInterface
{
    #[\Override]
    public static function getName(): string
    {
        return 'default';
    }

    #[\Override]
    public function renderMeta(array $parameters): array
    {
        return [];
    }
}
