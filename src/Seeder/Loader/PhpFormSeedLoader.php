<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Loader;

use Letkode\FormSchemaBundle\Seeder\Contract\FormSeederInterface;
use Letkode\FormSchemaBundle\Seeder\Contract\SeedLoaderInterface;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

final class PhpFormSeedLoader implements SeedLoaderInterface
{
    /** @param iterable<FormSeederInterface> $seeders */
    public function __construct(private readonly iterable $seeders)
    {
    }

    /** @return list<SeedSource> */
    public function load(?string $filter = null): array
    {
        $sources = [];

        foreach ($this->seeders as $seeder) {
            $data     = $seeder->getFormData();
            $tag      = (string) ($data['tag'] ?? '');
            $checksum = hash('sha256', json_encode(['form' => $data], JSON_THROW_ON_ERROR));

            if ($filter !== null && $tag !== $filter) {
                continue;
            }

            $sources[] = new SeedSource(
                tag: $tag,
                data: ['form' => $data],
                checksum: $checksum,
                sourceName: $seeder::class,
            );
        }

        return $sources;
    }
}
