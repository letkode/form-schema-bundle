<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Contract;

use Letkode\FormSchemaBundle\Application\DTO\FormDTO;

interface FormSchemaResolverInterface
{
    public function schema(string $tag): static;

    public function withLocale(string $locale): static;

    public function withContext(string $action): static;

    /** @param array<string> $tags */
    public function includingSections(array $tags): static;

    /** @param array<string> $tags */
    public function excludingSections(array $tags): static;

    /** @param array<string> $tags */
    public function includingGroups(array $tags): static;

    /** @param array<string> $tags */
    public function excludingGroups(array $tags): static;

    public function resolve(): FormDTO;

    /** @return array<string, mixed> */
    public function toArray(): array;
}
