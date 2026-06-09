<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\GroupRender;

use Letkode\FormSchemaBundle\Attribute\AsGroupRender;
use Letkode\FormSchemaBundle\Domain\Contract\GroupRenderInterface;

#[AsGroupRender]
final class DefaultGroupRender implements GroupRenderInterface
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
