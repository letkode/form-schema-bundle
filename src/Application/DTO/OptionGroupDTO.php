<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\DTO;

final readonly class OptionGroupDTO implements \JsonSerializable
{
    /**
     * @param list<OptionDTO> $options
     */
    public function __construct(
        public string $label,
        public array $options,
    ) {
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'options' => array_map(static fn (OptionDTO $o) => $o->jsonSerialize(), $this->options),
        ];
    }
}
