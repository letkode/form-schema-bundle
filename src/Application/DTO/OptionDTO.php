<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\DTO;

final readonly class OptionDTO implements \JsonSerializable
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public string|int $value,
        public string $label,
        public string|null $tag = null,
        public string|null $icon = null,
        public string|null $color = null,
        public int $position = 0,
        public array $data = [],
    ) {
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label,
            'tag' => $this->tag,
            'icon' => $this->icon,
            'color' => $this->color,
            'position' => $this->position,
            'data' => $this->data,
        ];
    }
}
