<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\ValueObject;

final readonly class SeedSource
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $tag,
        public array $data,
        public string $checksum,
        public string $sourceName,
    ) {
    }
}
