<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Entity\FormField;
use Letkode\FormSchemaBundle\Domain\Entity\FormGroup;
use Letkode\FormSchemaBundle\Domain\Entity\FormSection;
use Letkode\FormSchemaBundle\Domain\Repository\FormRepositoryInterface;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository\FormFieldRepository;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository\FormGroupRepository;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository\FormSectionRepository;
use Letkode\FormSchemaBundle\Seeder\Validator\FormSeedValidator;
use Letkode\FormSchemaBundle\Seeder\ValueObject\ProcessorResult;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

final class FormSeedProcessor
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository,
        private readonly FormSectionRepository $sectionRepository,
        private readonly FormGroupRepository $groupRepository,
        private readonly FormFieldRepository $fieldRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FormSeedValidator $validator,
    ) {
    }

    public function process(SeedSource $source, bool $prune = false, bool $force = false): ProcessorResult
    {
        $errors = $this->validator->validate($source->data);

        if ([] !== $errors) {
            return ProcessorResult::error($source->tag, $source->sourceName, $errors);
        }

        /** @var array<string, mixed> $formData */
        $formData = $source->data['form'];

        $form = $this->formRepository->findOneByTag($source->tag);
        $created = null === $form;

        if (!$created && !$force && $form->seedChecksum === $source->checksum) {
            return ProcessorResult::skipped($source->tag, $source->sourceName);
        }

        try {
            if ($created) {
                $form = $this->createForm($formData);
            } else {
                $this->updateForm($form, $formData);
            }

            $this->processSections($form, $formData['sections'] ?? [], $prune);

            $form->updateSeedChecksum($source->checksum);
            $this->formRepository->save($form, true);
        } catch (\Throwable $e) {
            return ProcessorResult::error($source->tag, $source->sourceName, [$e->getMessage()]);
        }

        return $created
            ? ProcessorResult::created($source->tag, $source->sourceName)
            : ProcessorResult::updated($source->tag, $source->sourceName);
    }

    /** @param array<string, mixed> $data */
    private function createForm(array $data): Form
    {
        $form = new Form();
        $form->name = (string) $data['name'];
        $form->enabled = (bool) ($data['enabled'] ?? true);
        $form->defaultLang = (string) ($data['default_lang'] ?? 'es');
        $form->parameters = (array) ($data['parameters'] ?? []);
        $form->setTag((string) $data['tag']);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $form->translations = $data['translations'];
        }

        $this->formRepository->save($form);

        return $form;
    }

    /** @param array<string, mixed> $data */
    private function updateForm(Form $form, array $data): void
    {
        $form->name = (string) $data['name'];
        $form->enabled = (bool) ($data['enabled'] ?? true);
        $form->defaultLang = (string) ($data['default_lang'] ?? 'es');
        $form->parameters = (array) ($data['parameters'] ?? []);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $form->translations = $data['translations'];
        }
    }

    /**
     * @param list<array<string, mixed>> $sectionsData
     */
    private function processSections(Form $form, array $sectionsData, bool $prune): void
    {
        $processedTags = [];

        foreach ($sectionsData as $sectionData) {
            /** @var array<string, mixed> $sectionData */
            $tag = (string) $sectionData['tag'];
            $section = $this->sectionRepository->findOneBy(['form' => $form, 'tag' => $tag]);

            if (null === $section) {
                $section = $this->createSection($sectionData, $form);
            } else {
                $this->updateSection($section, $sectionData);
            }

            $this->processGroups($section, $sectionData['groups'] ?? [], $prune);
            $this->entityManager->persist($section);

            $processedTags[] = $tag;
        }

        if ($prune) {
            $this->pruneChildren(
                array_values($this->sectionRepository->findBy(['form' => $form])),
                $processedTags,
                fn (FormSection $s) => $this->sectionRepository->remove($s),
                static fn (FormSection $s) => $s->tag,
            );
        }
    }

    /** @param array<string, mixed> $data */
    private function createSection(array $data, Form $form): FormSection
    {
        $section = new FormSection();
        $section->name = (string) $data['name'];
        $section->description = isset($data['description']) ? (string) $data['description'] : null;
        $section->position = (int) ($data['position'] ?? 0);
        $section->enabled = (bool) ($data['enabled'] ?? true);
        $section->parameters = (array) ($data['parameters'] ?? []);
        $section->setTag((string) $data['tag']);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $section->translations = $data['translations'];
        }

        $form->addSection($section);

        return $section;
    }

    /** @param array<string, mixed> $data */
    private function updateSection(FormSection $section, array $data): void
    {
        $section->name = (string) $data['name'];
        $section->description = isset($data['description']) ? (string) $data['description'] : null;
        $section->position = (int) ($data['position'] ?? 0);
        $section->enabled = (bool) ($data['enabled'] ?? true);
        $section->parameters = (array) ($data['parameters'] ?? []);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $section->translations = $data['translations'];
        }
    }

    /**
     * @param list<array<string, mixed>> $groupsData
     */
    private function processGroups(FormSection $section, array $groupsData, bool $prune): void
    {
        $processedTags = [];

        foreach ($groupsData as $groupData) {
            /** @var array<string, mixed> $groupData */
            $tag = (string) $groupData['tag'];
            $group = $this->groupRepository->findOneBy(['section' => $section, 'tag' => $tag]);

            if (null === $group) {
                $group = $this->createGroup($groupData, $section);
            } else {
                $this->updateGroup($group, $groupData);
            }

            $this->processFields($group, $groupData['fields'] ?? [], $prune);
            $this->entityManager->persist($group);

            $processedTags[] = $tag;
        }

        if ($prune) {
            $this->pruneChildren(
                array_values($this->groupRepository->findBy(['section' => $section])),
                $processedTags,
                fn (FormGroup $g) => $this->groupRepository->remove($g),
                static fn (FormGroup $g) => $g->tag,
            );
        }
    }

    /** @param array<string, mixed> $data */
    private function createGroup(array $data, FormSection $section): FormGroup
    {
        $group = new FormGroup();
        $group->name = (string) $data['name'];
        $group->description = isset($data['description']) ? (string) $data['description'] : null;
        $group->position = (int) ($data['position'] ?? 0);
        $group->enabled = (bool) ($data['enabled'] ?? true);
        $group->parameters = (array) ($data['parameters'] ?? []);
        $group->setTag((string) $data['tag']);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $group->translations = $data['translations'];
        }

        $section->addGroup($group);

        return $group;
    }

    /** @param array<string, mixed> $data */
    private function updateGroup(FormGroup $group, array $data): void
    {
        $group->name = (string) $data['name'];
        $group->description = isset($data['description']) ? (string) $data['description'] : null;
        $group->position = (int) ($data['position'] ?? 0);
        $group->enabled = (bool) ($data['enabled'] ?? true);
        $group->parameters = (array) ($data['parameters'] ?? []);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $group->translations = $data['translations'];
        }
    }

    /**
     * @param list<array<string, mixed>> $fieldsData
     */
    private function processFields(FormGroup $group, array $fieldsData, bool $prune): void
    {
        $processedTags = [];

        foreach ($fieldsData as $fieldData) {
            /** @var array<string, mixed> $fieldData */
            $tag = (string) $fieldData['tag'];
            $field = $this->fieldRepository->findOneBy(['group' => $group, 'tag' => $tag]);

            if (null === $field) {
                $field = $this->createField($fieldData, $group);
            } else {
                $this->updateField($field, $fieldData);
            }

            $this->entityManager->persist($field);
            $processedTags[] = $tag;
        }

        if ($prune) {
            $this->pruneChildren(
                array_values($this->fieldRepository->findBy(['group' => $group])),
                $processedTags,
                fn (FormField $f) => $this->fieldRepository->remove($f),
                static fn (FormField $f) => $f->tag,
            );
        }
    }

    /** @param array<string, mixed> $data */
    private function createField(array $data, FormGroup $group): FormField
    {
        $field = new FormField();
        $field->name = (string) $data['name'];
        $field->description = isset($data['description']) ? (string) $data['description'] : null;
        $field->type = (string) $data['type'];
        $field->position = (int) ($data['position'] ?? 0);
        $field->enabled = (bool) ($data['enabled'] ?? true);
        $field->attributes = (array) ($data['attributes'] ?? []);
        $field->interactions = isset($data['interactions']) ? (array) $data['interactions'] : null;
        $field->parameters = (array) ($data['parameters'] ?? []);
        $field->setTag((string) $data['tag']);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $field->translations = $data['translations'];
        }

        $group->addField($field);

        return $field;
    }

    /** @param array<string, mixed> $data */
    private function updateField(FormField $field, array $data): void
    {
        $field->name = (string) $data['name'];
        $field->description = isset($data['description']) ? (string) $data['description'] : null;
        $field->type = (string) $data['type'];
        $field->position = (int) ($data['position'] ?? 0);
        $field->enabled = (bool) ($data['enabled'] ?? true);
        $field->attributes = (array) ($data['attributes'] ?? []);
        $field->interactions = isset($data['interactions']) ? (array) $data['interactions'] : null;
        $field->parameters = (array) ($data['parameters'] ?? []);

        if (isset($data['translations']) && \is_array($data['translations'])) {
            $field->translations = $data['translations'];
        }
    }

    /**
     * @template T of object
     *
     * @param list<T>             $existingEntities
     * @param list<string>        $processedTags
     * @param callable(T): void   $removeCallback
     * @param callable(T): string $tagCallback
     */
    private function pruneChildren(
        array $existingEntities,
        array $processedTags,
        callable $removeCallback,
        callable $tagCallback,
    ): void {
        foreach ($existingEntities as $entity) {
            if (!\in_array($tagCallback($entity), $processedTags, true)) {
                $removeCallback($entity);
            }
        }
    }
}
