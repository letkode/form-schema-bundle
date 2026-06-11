<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\ValueObject;

final readonly class UniqueRule
{
    public function __construct(
        public bool $enabled = false,
        public string|null $entity = null,
        public string|null $method = null,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            enabled: $data['enabled'] ?? false,
            entity: $data['entity'] ?? null,
            method: $data['method'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'entity' => $this->entity,
            'method' => $this->method,
        ];
    }
}
