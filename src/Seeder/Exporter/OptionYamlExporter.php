<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Exporter;

use Letkode\FormSchemaBundle\Domain\Entity\FormOption;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionValue;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionRepositoryInterface;
use Symfony\Component\Yaml\Yaml;

final class OptionYamlExporter
{
    public function __construct(
        private readonly FormOptionRepositoryInterface $optionRepository,
    ) {
    }

    public function export(string $tag): string
    {
        $option = $this->optionRepository->findOneByTag($tag);

        if (null === $option) {
            throw new \InvalidArgumentException("Option with tag \"{$tag}\" not found.");
        }

        return Yaml::dump($this->optionToArray($option), inline: 4, indent: 2);
    }

    /** @return array<string, mixed> */
    private function optionToArray(FormOption $option): array
    {
        return [
            'option' => [
                'tag' => $option->tag,
                'name' => $option->name,
                'values' => array_values(
                    array_map(
                        fn (FormOptionValue $v) => $this->valueToArray($v),
                        $option->values->toArray(),
                    ),
                ),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function valueToArray(FormOptionValue $value): array
    {
        return [
            'tag' => $value->tag,
            'label' => $value->label,
            'description' => $value->description,
            'position' => $value->position,
            'enabled' => $value->enabled,
        ];
    }
}
