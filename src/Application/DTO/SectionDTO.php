<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\DTO;

final readonly class SectionDTO implements \JsonSerializable
{
    /**
     * @param array<string, mixed>      $parameters
     * @param array<string, mixed>      $renderMeta
     * @param array<string, mixed>|null $translations
     * @param list<GroupDTO>            $groups
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $tag,
        public string|null $description,
        public int $position,
        public bool $enabled,
        public array $parameters,
        public string $renderType,
        public array $renderMeta,
        public array|null $translations,
        public array $groups,
    ) {
    }

    /** @return array<string, mixed> */
    #[\Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'description' => $this->description,
            'position' => $this->position,
            'enabled' => $this->enabled,
            'parameters' => $this->parameters,
            'render' => ['type' => $this->renderType, 'metadata' => $this->renderMeta],
            'translations' => $this->translations,
            'groups' => array_map(static fn (GroupDTO $g) => $g->jsonSerialize(), $this->groups),
        ];
    }
}
