<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Loader;

use Letkode\FormSchemaBundle\Seeder\Contract\SeedLoaderInterface;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;
use Symfony\Component\Yaml\Yaml;

final class YamlOptionSeedLoader implements SeedLoaderInterface
{
    public function __construct(private readonly string $seedsPath)
    {
    }

    /** @return list<SeedSource> */
    public function load(string|null $filter = null): array
    {
        $dir = rtrim($this->seedsPath, '/') . '/options';

        if (!is_dir($dir)) {
            return [];
        }

        $sources = [];

        foreach (glob($dir . '/*.yaml') ?: [] as $path) {
            $filename = basename($path, '.yaml');

            if (null !== $filter && $filename !== $filter) {
                continue;
            }

            $rawContent = file_get_contents($path);

            if (false === $rawContent) {
                continue;
            }

            /** @var array<string, mixed> $data */
            $data = Yaml::parse($rawContent) ?? [];
            $tag = (string) ($data['option']['tag'] ?? $filename);
            $checksum = hash('sha256', $rawContent);

            $sources[] = new SeedSource(
                tag: $tag,
                data: $data,
                checksum: $checksum,
                sourceName: basename($path),
            );
        }

        return $sources;
    }
}
