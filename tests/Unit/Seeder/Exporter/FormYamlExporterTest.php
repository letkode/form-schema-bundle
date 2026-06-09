<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Tests\Unit\Seeder\Exporter;

use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Exception\FormNotFoundException;
use Letkode\FormSchemaBundle\Domain\Repository\FormRepositoryInterface;
use Letkode\FormSchemaBundle\Seeder\Exporter\FormYamlExporter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FormYamlExporterTest extends TestCase
{
    private MockObject&FormRepositoryInterface $repository;
    private FormYamlExporter $exporter;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(FormRepositoryInterface::class);
        $this->exporter = new FormYamlExporter($this->repository);
    }

    #[Test]
    public function testExportThrowsWhenFormNotFound(): void
    {
        $this->repository->method('findOneByTag')->willReturn(null);

        $this->expectException(FormNotFoundException::class);

        $this->exporter->export('nonexistent');
    }

    #[Test]
    public function testExportReturnsValidYamlWithFormKey(): void
    {
        $form = new Form();
        $form->name = 'Contact Form';
        $form->setTag('contact');

        $this->repository->method('findOneByTag')->willReturn($form);

        $yaml = $this->exporter->export('contact');

        self::assertStringContainsString('form:', $yaml);
        self::assertStringContainsString('tag: contact', $yaml);
        self::assertStringContainsString('Contact Form', $yaml);
    }
}
