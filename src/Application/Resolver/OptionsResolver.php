<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\Resolver;

use Letkode\FormSchemaBundle\Application\DTO\OptionDTO;
use Letkode\FormSchemaBundle\Domain\Contract\OptionsSourceRegistryInterface;

final class OptionsResolver
{
    public function __construct(
        private readonly OptionsSourceRegistryInterface $registry,
    ) {
    }

    /** @return list<OptionDTO> */
    public function resolve(array $setOptions, string|null $locale = null): array
    {
        if (empty($setOptions) || !isset($setOptions['type'])) {
            return [];
        }

        $source = $this->registry->get($setOptions['type']);

        return $source->resolve($setOptions['params'] ?? [], $locale);
    }
}
