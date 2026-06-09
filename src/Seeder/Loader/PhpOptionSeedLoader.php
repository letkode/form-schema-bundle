<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Loader;

use Letkode\FormSchemaBundle\Seeder\Contract\OptionSeederInterface;
use Letkode\FormSchemaBundle\Seeder\Contract\SeedLoaderInterface;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

final class PhpOptionSeedLoader implements SeedLoaderInterface
{
    /** @param iterable<OptionSeederInterface> $seeders */
    public function __construct(private readonly iterable $seeders)
    {
    }

    /** @return list<SeedSource> */
    public function load(string|null $filter = null): array
    {
        $sources = [];

        foreach ($this->seeders as $seeder) {
            $data = $seeder->getOptionData();
            $tag = (string) ($data['tag'] ?? '');
            $checksum = hash('sha256', json_encode(['option' => $data], \JSON_THROW_ON_ERROR));

            if (null !== $filter && $tag !== $filter) {
                continue;
            }

            $sources[] = new SeedSource(
                tag: $tag,
                data: ['option' => $data],
                checksum: $checksum,
                sourceName: $seeder::class,
            );
        }

        return $sources;
    }
}
