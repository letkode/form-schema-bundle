<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\ValueObject;

final readonly class FieldInteraction
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

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            trigger: $data['trigger'],
            action: $data['action'],
            target: $data['target'] ?? null,
            condition: $data['condition'] ?? [],
            params: $data['params'] ?? [],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
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
