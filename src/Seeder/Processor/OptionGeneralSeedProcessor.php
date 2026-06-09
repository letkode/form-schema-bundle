<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneral;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneralValue;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionGeneralRepositoryInterface;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository\FormOptionGeneralValueRepository;
use Letkode\FormSchemaBundle\Seeder\Validator\OptionGeneralSeedValidator;
use Letkode\FormSchemaBundle\Seeder\ValueObject\ProcessorResult;
use Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource;

final class OptionGeneralSeedProcessor
{
    public function __construct(
        private readonly FormOptionGeneralRepositoryInterface $optionGeneralRepository,
        private readonly FormOptionGeneralValueRepository $valueRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly OptionGeneralSeedValidator $validator,
    ) {
    }

    public function process(SeedSource $source, bool $prune = false, bool $force = false): ProcessorResult
    {
        $errors = $this->validator->validate($source->data);

        if ([] !== $errors) {
            return ProcessorResult::error($source->tag, $source->sourceName, $errors);
        }

        /** @var array<string, mixed> $optionData */
        $optionData = $source->data['option_general'];

        $option = $this->optionGeneralRepository->findOneByTag($source->tag);
        $created = null === $option;

        if (!$created && !$force && $option->seedChecksum === $source->checksum) {
            return ProcessorResult::skipped($source->tag, $source->sourceName);
        }

        try {
            if ($created) {
                $option = $this->createOptionGeneral($optionData);
            } else {
                $this->updateOptionGeneral($option, $optionData);
            }

            $this->processValues($option, $optionData['values'] ?? [], $prune);

            $option->updateSeedChecksum($source->checksum);
            $this->optionGeneralRepository->save($option, true);
        } catch (\Throwable $e) {
            return ProcessorResult::error($source->tag, $source->sourceName, [$e->getMessage()]);
        }

        return $created
            ? ProcessorResult::created($source->tag, $source->sourceName)
            : ProcessorResult::updated($source->tag, $source->sourceName);
    }

    /** @param array<string, mixed> $data */
    private function createOptionGeneral(array $data): FormOptionGeneral
    {
        $option = new FormOptionGeneral();
        $option->name = (string) $data['name'];
        $option->setTag((string) $data['tag']);

        $this->optionGeneralRepository->save($option);

        return $option;
    }

    /** @param array<string, mixed> $data */
    private function updateOptionGeneral(FormOptionGeneral $option, array $data): void
    {
        $option->name = (string) $data['name'];
    }

    /**
     * @param list<array<string, mixed>> $valuesData
     */
    private function processValues(FormOptionGeneral $option, array $valuesData, bool $prune): void
    {
        $processedTags = [];

        foreach ($valuesData as $valueData) {
            /** @var array<string, mixed> $valueData */
            $tag = (string) $valueData['tag'];
            $value = $this->valueRepository->findOneBy(['group' => $option, 'tag' => $tag]);

            if (null === $value) {
                $value = $this->createValue($valueData, $option);
            } else {
                $this->updateValue($value, $valueData);
            }

            $this->entityManager->persist($value);
            $processedTags[] = $tag;
        }

        if ($prune) {
            foreach ($this->valueRepository->findBy(['group' => $option]) as $existing) {
                if (!\in_array($existing->tag, $processedTags, true)) {
                    $this->valueRepository->remove($existing);
                }
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function createValue(array $data, FormOptionGeneral $option): FormOptionGeneralValue
    {
        $value = new FormOptionGeneralValue();
        $value->label = (string) $data['label'];
        $value->description = isset($data['description']) ? (string) $data['description'] : null;
        $value->position = (int) ($data['position'] ?? 0);
        $value->enabled = (bool) ($data['enabled'] ?? true);
        $value->setTag((string) $data['tag']);

        $option->addValue($value);

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function updateValue(FormOptionGeneralValue $value, array $data): void
    {
        $value->label = (string) $data['label'];
        $value->description = isset($data['description']) ? (string) $data['description'] : null;
        $value->position = (int) ($data['position'] ?? 0);
        $value->enabled = (bool) ($data['enabled'] ?? true);
    }
}
