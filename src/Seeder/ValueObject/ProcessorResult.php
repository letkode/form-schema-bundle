<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\ValueObject;

use Letkode\FormSchemaBundle\Seeder\Enum\SeedStatus;

final readonly class ProcessorResult
{
    /** @param list<string> $errors */
    public function __construct(
        public string $tag,
        public string $sourceName,
        public SeedStatus $status,
        public array $errors = [],
    ) {
    }

    public static function created(string $tag, string $sourceName): self
    {
        return new self($tag, $sourceName, SeedStatus::Created);
    }

    public static function updated(string $tag, string $sourceName): self
    {
        return new self($tag, $sourceName, SeedStatus::Updated);
    }

    public static function skipped(string $tag, string $sourceName): self
    {
        return new self($tag, $sourceName, SeedStatus::Skipped);
    }

    /** @param list<string> $errors */
    public static function error(string $tag, string $sourceName, array $errors): self
    {
        return new self($tag, $sourceName, SeedStatus::Error, $errors);
    }
}
