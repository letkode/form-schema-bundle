<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Registry;

use Letkode\FormSchemaBundle\Domain\Contract\InteractionHandlerInterface;
use Letkode\FormSchemaBundle\Domain\Contract\InteractionHandlerRegistryInterface;
use Letkode\FormSchemaBundle\Domain\Exception\UnknownInteractionHandlerException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class InteractionHandlerRegistry implements InteractionHandlerRegistryInterface
{
    /** @var array<string, InteractionHandlerInterface> */
    private array $handlers = [];

    public function __construct(
        #[AutowireIterator('form_schema.interaction_handler')]
        iterable $taggedHandlers,
    ) {
        foreach ($taggedHandlers as $handler) {
            $this->handlers[$handler::getName()] = $handler;
        }
    }

    #[\Override]
    public function get(string $name): InteractionHandlerInterface
    {
        return $this->handlers[$name] ?? throw new UnknownInteractionHandlerException($name);
    }

    #[\Override]
    public function has(string $name): bool
    {
        return isset($this->handlers[$name]);
    }

    /** @return array<string, InteractionHandlerInterface> */
    #[\Override]
    public function all(): array
    {
        return $this->handlers;
    }
}
