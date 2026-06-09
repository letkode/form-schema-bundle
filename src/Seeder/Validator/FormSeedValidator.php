<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Validator;

use Letkode\FormSchemaBundle\Infrastructure\Registry\FieldTypeRegistry;

final class FormSeedValidator
{
    public function __construct(private readonly FieldTypeRegistry $fieldTypeRegistry)
    {
    }

    /**
     * Validates form seed data. Returns a list of error messages.
     *
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['form']) || !\is_array($data['form'])) {
            return ['Root key "form" is missing or not an array'];
        }

        $form = $data['form'];

        $errors = array_merge($errors, $this->validateStringField($form, 'tag', 'form', 100));
        $errors = array_merge($errors, $this->validateStringField($form, 'name', 'form'));

        if (isset($form['default_lang']) && !\is_string($form['default_lang'])) {
            $errors[] = 'form.default_lang must be a string';
        }

        if (isset($form['enabled']) && !\is_bool($form['enabled'])) {
            $errors[] = 'form.enabled must be a boolean';
        }

        if (isset($form['parameters']) && !\is_array($form['parameters'])) {
            $errors[] = 'form.parameters must be an array';
        }

        $sections = $form['sections'] ?? [];

        if (!\is_array($sections)) {
            $errors[] = 'form.sections must be an array';

            return $errors;
        }

        foreach ($sections as $si => $section) {
            if (!\is_array($section)) {
                $errors[] = "form.sections[{$si}] must be an array";
                continue;
            }

            $errors = array_merge($errors, $this->validateSection($section, $si));
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $section
     *
     * @return list<string>
     */
    private function validateSection(array $section, int $index): array
    {
        $prefix = "form.sections[{$index}]";
        $errors = [];

        $errors = array_merge($errors, $this->validateStringField($section, 'tag', $prefix, 100));
        $errors = array_merge($errors, $this->validateStringField($section, 'name', $prefix));
        $errors = array_merge($errors, $this->validatePositionField($section, $prefix));

        if (isset($section['enabled']) && !\is_bool($section['enabled'])) {
            $errors[] = "{$prefix}.enabled must be a boolean";
        }

        if (isset($section['parameters']) && !\is_array($section['parameters'])) {
            $errors[] = "{$prefix}.parameters must be an array";
        }

        $groups = $section['groups'] ?? [];

        if (!\is_array($groups)) {
            $errors[] = "{$prefix}.groups must be an array";

            return $errors;
        }

        foreach ($groups as $gi => $group) {
            if (!\is_array($group)) {
                $errors[] = "{$prefix}.groups[{$gi}] must be an array";
                continue;
            }

            $errors = array_merge($errors, $this->validateGroup($group, $index, $gi));
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $group
     *
     * @return list<string>
     */
    private function validateGroup(array $group, int $sectionIndex, int $index): array
    {
        $prefix = "form.sections[{$sectionIndex}].groups[{$index}]";
        $errors = [];

        $errors = array_merge($errors, $this->validateStringField($group, 'tag', $prefix, 100));
        $errors = array_merge($errors, $this->validateStringField($group, 'name', $prefix));
        $errors = array_merge($errors, $this->validatePositionField($group, $prefix));

        if (isset($group['enabled']) && !\is_bool($group['enabled'])) {
            $errors[] = "{$prefix}.enabled must be a boolean";
        }

        if (isset($group['parameters']) && !\is_array($group['parameters'])) {
            $errors[] = "{$prefix}.parameters must be an array";
        }

        $fields = $group['fields'] ?? [];

        if (!\is_array($fields)) {
            $errors[] = "{$prefix}.fields must be an array";

            return $errors;
        }

        foreach ($fields as $fi => $field) {
            if (!\is_array($field)) {
                $errors[] = "{$prefix}.fields[{$fi}] must be an array";
                continue;
            }

            $errors = array_merge($errors, $this->validateField($field, $sectionIndex, $index, $fi));
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $field
     *
     * @return list<string>
     */
    private function validateField(array $field, int $si, int $gi, int $index): array
    {
        $prefix = "form.sections[{$si}].groups[{$gi}].fields[{$index}]";
        $errors = [];

        $errors = array_merge($errors, $this->validateStringField($field, 'tag', $prefix, 100));
        $errors = array_merge($errors, $this->validateStringField($field, 'name', $prefix));
        $errors = array_merge($errors, $this->validatePositionField($field, $prefix));

        if (!isset($field['type'])) {
            $errors[] = "{$prefix}.type is required";
        } elseif (!\is_string($field['type'])) {
            $errors[] = "{$prefix}.type must be a string";
        } elseif (!$this->fieldTypeRegistry->has($field['type'])) {
            $knownTypes = implode(', ', array_keys($this->fieldTypeRegistry->all()));
            $errors[] = "{$prefix}.type \"{$field['type']}\" is unknown. Known types: {$knownTypes}";
        }

        if (isset($field['enabled']) && !\is_bool($field['enabled'])) {
            $errors[] = "{$prefix}.enabled must be a boolean";
        }

        if (isset($field['parameters']) && !\is_array($field['parameters'])) {
            $errors[] = "{$prefix}.parameters must be an array";
        }

        if (isset($field['attributes']) && !\is_array($field['attributes'])) {
            $errors[] = "{$prefix}.attributes must be an array";
        }

        if (isset($field['interactions']) && !\is_array($field['interactions'])) {
            $errors[] = "{$prefix}.interactions must be an array";
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    private function validateStringField(array $data, string $key, string $prefix, int $maxLength = 0): array
    {
        if (!isset($data[$key])) {
            return ["{$prefix}.{$key} is required"];
        }

        if (!\is_string($data[$key])) {
            return ["{$prefix}.{$key} must be a string"];
        }

        if ($maxLength > 0 && mb_strlen($data[$key]) > $maxLength) {
            return ["{$prefix}.{$key} must not exceed {$maxLength} characters"];
        }

        return [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    private function validatePositionField(array $data, string $prefix): array
    {
        if (!isset($data['position'])) {
            return [];
        }

        if (!\is_int($data['position']) || $data['position'] < 0) {
            return ["{$prefix}.position must be a non-negative integer"];
        }

        return [];
    }
}
