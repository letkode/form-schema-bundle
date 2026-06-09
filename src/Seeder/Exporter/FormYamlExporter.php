<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Exporter;

use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Entity\FormField;
use Letkode\FormSchemaBundle\Domain\Entity\FormGroup;
use Letkode\FormSchemaBundle\Domain\Entity\FormSection;
use Letkode\FormSchemaBundle\Domain\Exception\FormNotFoundException;
use Letkode\FormSchemaBundle\Domain\Repository\FormRepositoryInterface;
use Symfony\Component\Yaml\Yaml;

final class FormYamlExporter
{
    public function __construct(private readonly FormRepositoryInterface $formRepository)
    {
    }

    public function export(string $tag): string
    {
        $form = $this->formRepository->findOneByTag($tag);

        if (null === $form) {
            throw new FormNotFoundException($tag);
        }

        return Yaml::dump($this->formToArray($form), inline: 6, indent: 2);
    }

    /** @return array<string, mixed> */
    private function formToArray(Form $form): array
    {
        return [
            'form' => [
                'tag' => $form->tag,
                'name' => $form->name,
                'enabled' => $form->enabled,
                'default_lang' => $form->defaultLang,
                'parameters' => $form->parameters ?: [],
                'sections' => array_values(
                    array_map(
                        fn (FormSection $s) => $this->sectionToArray($s),
                        $form->sections->toArray(),
                    ),
                ),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function sectionToArray(FormSection $section): array
    {
        return [
            'tag' => $section->tag,
            'name' => $section->name,
            'description' => $section->description,
            'position' => $section->position,
            'enabled' => $section->enabled,
            'parameters' => $section->parameters ?: [],
            'groups' => array_values(
                array_map(
                    fn (FormGroup $g) => $this->groupToArray($g),
                    $section->groups->toArray(),
                ),
            ),
        ];
    }

    /** @return array<string, mixed> */
    private function groupToArray(FormGroup $group): array
    {
        return [
            'tag' => $group->tag,
            'name' => $group->name,
            'description' => $group->description,
            'position' => $group->position,
            'enabled' => $group->enabled,
            'parameters' => $group->parameters ?: [],
            'fields' => array_values(
                array_map(
                    fn (FormField $f) => $this->fieldToArray($f),
                    $group->fields->toArray(),
                ),
            ),
        ];
    }

    /** @return array<string, mixed> */
    private function fieldToArray(FormField $field): array
    {
        return [
            'tag' => $field->tag,
            'name' => $field->name,
            'description' => $field->description,
            'type' => $field->type,
            'position' => $field->position,
            'enabled' => $field->enabled,
            'parameters' => $field->parameters ?: [],
            'attributes' => $field->attributes ?: [],
            'interactions' => $field->interactions,
        ];
    }
}
