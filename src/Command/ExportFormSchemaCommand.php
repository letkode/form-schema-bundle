<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Command;

use Letkode\FormSchemaBundle\Seeder\Exporter\FormYamlExporter;
use Letkode\FormSchemaBundle\Seeder\Exporter\OptionYamlExporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'letkode:form-schema:export',
    description: 'Export a form or option to YAML',
)]
final class ExportFormSchemaCommand extends Command
{
    public function __construct(
        private readonly FormYamlExporter $formExporter,
        private readonly OptionYamlExporter $optionExporter,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type to export: "form" or "option"')
            ->addArgument('tag', InputArgument::REQUIRED, 'Tag of the element to export')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Write output to this file path instead of stdout');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = (string) $input->getArgument('type');
        $tag = (string) $input->getArgument('tag');
        $outputPath = null !== $input->getOption('output') ? (string) $input->getOption('output') : null;

        if (!\in_array($type, ['form', 'option'], true)) {
            $io->error('Invalid type. Use "form" or "option".');

            return Command::FAILURE;
        }

        try {
            $yaml = 'form' === $type
                ? $this->formExporter->export($tag)
                : $this->optionExporter->export($tag);
        } catch (\Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        if (null !== $outputPath) {
            $written = file_put_contents($outputPath, $yaml);

            if (false === $written) {
                $io->error(\sprintf('Could not write to file "%s".', $outputPath));

                return Command::FAILURE;
            }

            $io->success(\sprintf('Exported %s "%s" to %s', $type, $tag, $outputPath));

            return Command::SUCCESS;
        }

        $output->write($yaml);

        return Command::SUCCESS;
    }
}
