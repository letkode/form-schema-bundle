<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Tests\Unit\Infrastructure\FieldType;

use Letkode\FormSchemaBundle\Infrastructure\FieldType\CheckboxFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\ComboboxFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\DateFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\DateRangeFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\DateTimeFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\DuallistFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\EmailFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\FileFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\HiddenFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\NumberFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\PasswordFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\PhoneFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\PinFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\RadioFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\RangeFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\RatingFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\SelectFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\SelectMultipleFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\StringFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\SwitchFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\TextareaFieldType;
use Letkode\FormSchemaBundle\Infrastructure\FieldType\TreeFieldType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldTypeTest extends TestCase
{
    #[Test]
    public function testStringFieldTypeDoesNotTakeOptions(): void
    {
        self::assertFalse(new StringFieldType()->takesOptions());
    }

    #[Test]
    public function testSelectFieldTypeTakesOptions(): void
    {
        self::assertTrue(new SelectFieldType()->takesOptions());
    }

    #[Test]
    public function testSwitchFieldTypeTakesOptions(): void
    {
        self::assertTrue(new SwitchFieldType()->takesOptions());
    }

    #[Test]
    public function testDateFieldTypeFormatsModifier(): void
    {
        $result = new DateFieldType()->formatDefaultValue('+1 day');

        self::assertIsString($result);
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    #[Test]
    public function testDateFieldTypeReturnsNullForNull(): void
    {
        self::assertNull(new DateFieldType()->formatDefaultValue(null));
    }

    #[Test]
    public function testDateRangeFieldTypeReturnsArray(): void
    {
        $result = new DateRangeFieldType()->formatDefaultValue(null);

        self::assertIsArray($result);
        self::assertArrayHasKey('from', $result);
        self::assertArrayHasKey('to', $result);
        self::assertNull($result['from']);
        self::assertNull($result['to']);
    }

    #[Test]
    public function testRangeFieldTypeCastsToInt(): void
    {
        self::assertSame(42, new RangeFieldType()->formatDefaultValue('42'));
        self::assertNull(new RangeFieldType()->formatDefaultValue(null));
    }

    #[Test]
    public function testPinFieldTypeCastsToString(): void
    {
        self::assertSame('1234', new PinFieldType()->formatDefaultValue(1234));
        self::assertNull(new PinFieldType()->formatDefaultValue(null));
    }

    #[Test]
    public function testDuallistFieldTypeReturnsArray(): void
    {
        self::assertSame([], new DuallistFieldType()->formatDefaultValue(null));
        self::assertSame(['a'], new DuallistFieldType()->formatDefaultValue('a'));
        self::assertSame(['a', 'b'], new DuallistFieldType()->formatDefaultValue(['a', 'b']));
    }

    #[Test]
    public function testSelectFieldTypeDefaultParamsHasNoMultiple(): void
    {
        $params = new SelectFieldType()->getDefaultParams();
        self::assertArrayNotHasKey('multiple', $params);
    }

    #[Test]
    public function testSelectMultipleFieldTypeTakesOptions(): void
    {
        self::assertTrue(new SelectMultipleFieldType()->takesOptions());
    }

    #[Test]
    public function testSelectMultipleFieldTypeFormatsToArray(): void
    {
        $type = new SelectMultipleFieldType();
        self::assertSame([], $type->formatDefaultValue(null));
        self::assertSame(['es'], $type->formatDefaultValue('es'));
        self::assertSame(['es', 'en'], $type->formatDefaultValue(['es', 'en']));
    }

    #[Test]
    public function testAllFieldTypesHaveUniqueName(): void
    {
        $types = [
            new CheckboxFieldType(),
            new ComboboxFieldType(),
            new DateFieldType(),
            new DateRangeFieldType(),
            new DateTimeFieldType(),
            new DuallistFieldType(),
            new EmailFieldType(),
            new FileFieldType(),
            new HiddenFieldType(),
            new NumberFieldType(),
            new PasswordFieldType(),
            new PhoneFieldType(),
            new PinFieldType(),
            new RadioFieldType(),
            new RangeFieldType(),
            new RatingFieldType(),
            new SelectFieldType(),
            new SelectMultipleFieldType(),
            new StringFieldType(),
            new SwitchFieldType(),
            new TextareaFieldType(),
            new TreeFieldType(),
        ];

        $names = array_map(static fn ($t) => $t::getName(), $types);
        $unique = array_unique($names);

        self::assertCount(22, $types);
        self::assertCount(\count($names), $unique, 'Duplicate field type names found: ' . implode(', ', array_diff_assoc($names, $unique)));
    }
}
