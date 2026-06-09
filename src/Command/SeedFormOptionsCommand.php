<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Command;

use Letkode\FormSchemaBundle\Seeder\Enum\SeedStatus;
use Letkode\FormSchemaBundle\Seeder\Loader\PhpOptionSeedLoader;
use Letkode\FormSchemaBundle\Seeder\Loader\YamlOptionSeedLoader;
use Letkode\FormSchemaBundle\Seeder\Processor\OptionSeedProcessor;
use Letkode\FormSchemaBundle\Seeder\ValueObject\ProcessorResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'letkode:form-schema:seed:options',
    description: 'Seed general options from YAML files or PHP class seeders',
)]
final class SeedFormOptionsCommand extends Command
{
    public function __construct(
        private readonly YamlOptionSeedLoader $yamlLoader,
        private readonly PhpOptionSeedLoader $phpLoader,
        private readonly OptionSeedProcessor $processor,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('option', null, InputOption::VALUE_REQUIRED, 'Process only the option general with this tag')
            ->addOption('prune', null, InputOption::VALUE_NONE, 'Soft-delete orphan values not present in seed')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Re-seed even if checksum has not changed');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filter = null !== $input->getOption('option') ? (string) $input->getOption('option') : null;
        $prune = (bool) $input->getOption('prune');
        $force = (bool) $input->getOption('force');

        $sources = array_merge(
            $this->yamlLoader->load($filter),
            $this->phpLoader->load($filter),
        );

        if ([] === $sources) {
            $io->warning(null !== $filter
                ? "No option general found with tag \"{$filter}\"."
                : 'No seed files or PHP class seeders found.');

            return Command::SUCCESS;
        }

        $this->assertNoDuplicateTags($sources, $io);

        $results = [];

        foreach ($sources as $source) {
            $results[] = $this->processor->process($source, $prune, $force);
        }

        $this->renderResultsTable($output, $results);

        $errors = array_filter($results, static fn (ProcessorResult $r) => SeedStatus::Error === $r->status);

        if ([] !== $errors) {
            return Command::FAILURE;
        }

        $io->success(\sprintf('Processed %d option(s).', \count($results)));

        return Command::SUCCESS;
    }

    /**
     * @param list<\Letkode\FormSchemaBundle\Seeder\ValueObject\SeedSource> $sources
     */
    private function assertNoDuplicateTags(array $sources, SymfonyStyle $io): void
    {
        $tags = array_map(static fn ($s) => $s->tag, $sources);
        $dupes = array_keys(array_filter(array_count_values($tags), static fn (int $c) => $c > 1));

        if ([] !== $dupes) {
            $io->error('Duplicate seed tags found: ' . implode(', ', $dupes));
            exit(Command::FAILURE);
        }
    }

    /**
     * @param list<ProcessorResult> $results
     */
    private function renderResultsTable(OutputInterface $output, array $results): void
    {
        $table = new Table($output);
        $table->setHeaders(['Source', 'Tag', 'Status', 'Errors']);

        foreach ($results as $result) {
            $statusLabel = match ($result->status) {
                SeedStatus::Created => '<info>created</info>',
                SeedStatus::Updated => '<comment>updated</comment>',
                SeedStatus::Skipped => 'skipped',
                SeedStatus::Error => '<error>error</error>',
            };

            $table->addRow([
                $result->sourceName,
                $result->tag,
                $statusLabel,
                implode("\n", $result->errors),
            ]);
        }

        $table->render();
    }
}
