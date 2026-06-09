<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\FormRender;

use Letkode\FormSchemaBundle\Attribute\AsFormRender;
use Letkode\FormSchemaBundle\Domain\Contract\FormRenderInterface;

#[AsFormRender]
final class DefaultFormRender implements FormRenderInterface
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
