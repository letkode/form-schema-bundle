<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Loader;

use Letkode\FormSchemaBundle\Seeder\Contract\OptionGeneralSeederInterface;
use Letkode\FormSchemaBundle\Seeder\Contract\SeedLoaderInterface;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

final class PhpOptionGeneralSeedLoader implements SeedLoaderInterface
{
    /** @param iterable<OptionGeneralSeederInterface> $seeders */
    public function __construct(private readonly iterable $seeders)
    {
    }

    /** @return list<SeedSource> */
    public function load(?string $filter = null): array
    {
        $sources = [];

        foreach ($this->seeders as $seeder) {
            $data     = $seeder->getOptionGeneralData();
            $tag      = (string) ($data['tag'] ?? '');
            $checksum = hash('sha256', json_encode(['option_general' => $data], JSON_THROW_ON_ERROR));

            if ($filter !== null && $tag !== $filter) {
                continue;
            }

            $sources[] = new SeedSource(
                tag: $tag,
                data: ['option_general' => $data],
                checksum: $checksum,
                sourceName: $seeder::class,
            );
        }

        return $sources;
    }
}
