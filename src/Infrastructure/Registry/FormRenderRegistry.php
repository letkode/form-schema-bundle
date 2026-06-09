<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Registry;

use Letkode\FormSchemaBundle\Domain\Contract\FormRenderInterface;
use Letkode\FormSchemaBundle\Domain\Exception\UnknownRenderException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class FormRenderRegistry
{
    /** @var array<string, FormRenderInterface> */
    private array $renders = [];

    public function __construct(
        #[AutowireIterator('form_schema.form_render', defaultPriorityMethod: 'getPriority')]
        iterable $taggedRenders,
    ) {
        foreach ($taggedRenders as $render) {
            $this->renders[$render::getName()] = $render;
        }
    }

    public function get(string $name): FormRenderInterface
    {
        return $this->renders[$name] ?? throw new UnknownRenderException('form', $name);
    }

    public function has(string $name): bool
    {
        return isset($this->renders[$name]);
    }

    /** @return array<string, FormRenderInterface> */
    public function all(): array
    {
        return $this->renders;
    }
}
