<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Tests\Unit\Seeder\Validator;

use Letkode\FormSchemaBundle\Seeder\Validator\OptionSeedValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OptionSeedValidatorTest extends TestCase
{
    private OptionSeedValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new OptionSeedValidator();
    }

    #[Test]
    public function testValidDataPassesValidation(): void
    {
        $errors = $this->validator->validate($this->validData());

        self::assertSame([], $errors);
    }

    #[Test]
    public function testMissingRootKeyReturnsError(): void
    {
        $errors = $this->validator->validate([]);

        self::assertContains('Root key "option" is missing or not an array', $errors);
    }

    #[Test]
    public function testMissingTagReturnsError(): void
    {
        $data = $this->validData();
        unset($data['option']['tag']);

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'option.tag')));
    }

    #[Test]
    public function testMissingValueLabelReturnsError(): void
    {
        $data = $this->validData();
        unset($data['option']['values'][0]['label']);

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'label')));
    }

    #[Test]
    public function testInvalidValuePositionReturnsError(): void
    {
        $data = $this->validData();
        $data['option']['values'][0]['position'] = -5;

        $errors = $this->validator->validate($data);

        self::assertNotEmpty($errors);
        self::assertTrue(array_any($errors, static fn (string $e) => str_contains($e, 'position')));
    }

    /** @return array<string, mixed> */
    private function validData(): array
    {
        return [
            'option' => [
                'tag' => 'countries',
                'name' => 'Countries',
                'values' => [
                    ['tag' => 'mx', 'label' => 'Mexico', 'position' => 0, 'enabled' => true],
                    ['tag' => 'us', 'label' => 'United States', 'position' => 1, 'enabled' => true],
                ],
            ],
        ];
    }
}
