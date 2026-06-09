<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Exporter;

use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneral;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneralValue;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionGeneralRepositoryInterface;
use Symfony\Component\Yaml\Yaml;

final class OptionGeneralYamlExporter
{
    public function __construct(
        private readonly FormOptionGeneralRepositoryInterface $optionGeneralRepository,
    ) {
    }

    public function export(string $tag): string
    {
        $option = $this->optionGeneralRepository->findOneByTag($tag);

        if (null === $option) {
            throw new \InvalidArgumentException("Option general with tag \"{$tag}\" not found.");
        }

        return Yaml::dump($this->optionToArray($option), inline: 4, indent: 2);
    }

    /** @return array<string, mixed> */
    private function optionToArray(FormOptionGeneral $option): array
    {
        return [
            'option_general' => [
                'tag' => $option->tag,
                'name' => $option->name,
                'values' => array_values(
                    array_map(
                        fn (FormOptionGeneralValue $v) => $this->valueToArray($v),
                        $option->values->toArray(),
                    ),
                ),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function valueToArray(FormOptionGeneralValue $value): array
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
