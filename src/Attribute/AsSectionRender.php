<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class AsSectionRender
{
    public function __construct(public int $priority = 0)
    {
    }
}
