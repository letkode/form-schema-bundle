<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Command;

use Letkode\FormSchemaBundle\Domain\Repository\FormOptionRepositoryInterface;
use Letkode\FormSchemaBundle\Domain\Repository\FormRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'letkode:form-schema:remove',
    description: 'Soft-delete a form or option by type and tag',
)]
final class RemoveFormSchemaCommand extends Command
{
    public function __construct(
        private readonly FormRepositoryInterface $formRepository,
        private readonly FormOptionRepositoryInterface $optionRepository,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type to remove: "form" or "option"')
            ->addArgument('tag', InputArgument::REQUIRED, 'Tag of the element to remove');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = (string) $input->getArgument('type');
        $tag = (string) $input->getArgument('tag');

        if (!\in_array($type, ['form', 'option'], true)) {
            $io->error('Invalid type. Use "form" or "option".');

            return Command::FAILURE;
        }

        $confirmed = $io->confirm(
            \sprintf('Are you sure you want to remove %s "%s"? This action soft-deletes all related data.', $type, $tag),
            false,
        );

        if (!$confirmed) {
            $io->note('Operation cancelled.');

            return Command::SUCCESS;
        }

        if ('form' === $type) {
            return $this->removeForm($tag, $io);
        }

        return $this->removeOption($tag, $io);
    }

    private function removeForm(string $tag, SymfonyStyle $io): int
    {
        $form = $this->formRepository->findOneByTag($tag);

        if (null === $form) {
            $io->error(\sprintf('Form with tag "%s" not found.', $tag));

            return Command::FAILURE;
        }

        $this->formRepository->remove($form, true);
        $io->success(\sprintf('Form "%s" has been removed.', $tag));

        return Command::SUCCESS;
    }

    private function removeOption(string $tag, SymfonyStyle $io): int
    {
        $option = $this->optionRepository->findOneByTag($tag);

        if (null === $option) {
            $io->error(\sprintf('Option with tag "%s" not found.', $tag));

            return Command::FAILURE;
        }

        $this->optionRepository->remove($option, true);
        $io->success(\sprintf('Option "%s" has been removed.', $tag));

        return Command::SUCCESS;
    }
}
