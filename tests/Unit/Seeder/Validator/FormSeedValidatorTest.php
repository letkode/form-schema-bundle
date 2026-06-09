<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Tests\Unit\Seeder\Validator;

use Letkode\FormSchemaBundle\Infrastructure\FieldType\StringFieldType;
use Letkode\FormSchemaBundle\Infrastructure\Registry\FieldTypeRegistry;
use Letkode\FormSchemaBundle\Seeder\Validator\FormSeedValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FormSeedValidatorTest extends TestCase
{
    private FormSeedValidator $validator;

    protected function setUp(): void
    {
        $registry = new FieldTypeRegistry(new \ArrayIterator([new StringFieldType()]));
        $this->validator = new FormSeedValidator($registry);
    }

    #[Test]
    public function testValidFormPassesValidation(): void
    {
        $errors = $this->validator->validate($this->validFormData());

        self::assertSame([], $errors);
    }

    #[Test]
    public function testMissingRootFormKeyReturnsError(): void
    {
        $errors = $this->validator->validate([]);

        self::assertContains('Root key "form" is missing or not an array', $errors);
    }

    #[Test]
    public function testMissingFormTagReturnsError(): void
    {
        $data = $this->validFormData();
        unset($data['form']['tag']);

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertStringContainsString('form.tag', $errors[0]);
    }

    #[Test]
    public function testMissingFormNameReturnsError(): void
    {
        $data = $this->validFormData();
        unset($data['form']['name']);

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'form.name')));
    }

    #[Test]
    public function testTagExceedingMaxLengthReturnsError(): void
    {
        $data = $this->validFormData();
        $data['form']['tag'] = str_repeat('a', 101);

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, '100 characters')));
    }

    #[Test]
    public function testUnknownFieldTypeReturnsError(): void
    {
        $data = $this->validFormData();
        $data['form']['sections'][0]['groups'][0]['fields'][0]['type'] = 'unknown_type';

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'unknown_type')));
    }

    #[Test]
    public function testInvalidPositionReturnsError(): void
    {
        $data = $this->validFormData();
        $data['form']['sections'][0]['groups'][0]['fields'][0]['position'] = -1;

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'position')));
    }

    #[Test]
    public function testInvalidEnabledValueReturnsError(): void
    {
        $data = $this->validFormData();
        $data['form']['enabled'] = 'yes';

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'enabled')));
    }

    /** @return array<string, mixed> */
    private function validFormData(): array
    {
        return [
            'form' => [
                'tag' => 'contact',
                'name' => 'Contact Form',
                'enabled' => true,
                'default_lang' => 'es',
                'parameters' => ['type_render' => 'default'],
                'sections' => [
                    [
                        'tag' => 'personal',
                        'name' => 'Personal',
                        'position' => 0,
                        'enabled' => true,
                        'groups' => [
                            [
                                'tag' => 'names',
                                'name' => 'Names',
                                'position' => 0,
                                'enabled' => true,
                                'fields' => [
                                    [
                                        'tag' => 'first_name',
                                        'name' => 'First Name',
                                        'type' => 'string',
                                        'position' => 0,
                                        'enabled' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
