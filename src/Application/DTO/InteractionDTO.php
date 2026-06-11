<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\DTO;

use Letkode\FormSchemaBundle\Domain\ValueObject\FieldInteraction;

final readonly class InteractionDTO implements \JsonSerializable
{
    /**
     * @param string|array<mixed>|null $target
     * @param array<string, mixed>     $condition
     * @param array<string, mixed>     $params
     */
    public function __construct(
        public string $trigger,
        public string $action,
        public string|array|null $target,
        public array $condition,
        public array $params,
    ) {
    }

    /**
     * @param array<string, mixed>|null $mergedParams
     */
    public static function fromInteraction(FieldInteraction $interaction, array|null $mergedParams = null): self
    {
        return new self(
            trigger: $interaction->trigger,
            action: $interaction->action,
            target: $interaction->target,
            condition: $interaction->condition,
            params: $mergedParams ?? $interaction->params,
        );
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'trigger' => $this->trigger,
            'action' => $this->action,
            'target' => $this->target,
            'condition' => $this->condition,
            'params' => $this->params,
        ];
    }
}
