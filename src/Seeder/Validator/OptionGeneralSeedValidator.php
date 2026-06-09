<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Validator;

final class OptionGeneralSeedValidator
{
    /**
     * Validates option general seed data. Returns a list of error messages.
     *
     * @param array<string, mixed> $data
     * @return list<string>
     */
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['option_general']) || !is_array($data['option_general'])) {
            return ['Root key "option_general" is missing or not an array'];
        }

        $option = $data['option_general'];

        $errors = array_merge($errors, $this->validateStringField($option, 'tag', 'option_general', 100));
        $errors = array_merge($errors, $this->validateStringField($option, 'name', 'option_general'));

        $values = $option['values'] ?? [];

        if (!is_array($values)) {
            $errors[] = 'option_general.values must be an array';

            return $errors;
        }

        foreach ($values as $vi => $value) {
            if (!is_array($value)) {
                $errors[] = "option_general.values[{$vi}] must be an array";
                continue;
            }

            $errors = array_merge($errors, $this->validateValue($value, $vi));
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $value
     * @return list<string>
     */
    private function validateValue(array $value, int $index): array
    {
        $prefix = "option_general.values[{$index}]";
        $errors = [];

        $errors = array_merge($errors, $this->validateStringField($value, 'tag', $prefix, 100));
        $errors = array_merge($errors, $this->validateStringField($value, 'label', $prefix));

        if (isset($value['enabled']) && !is_bool($value['enabled'])) {
            $errors[] = "{$prefix}.enabled must be a boolean";
        }

        if (isset($value['position']) && (!is_int($value['position']) || $value['position'] < 0)) {
            $errors[] = "{$prefix}.position must be a non-negative integer";
        }

        if (isset($value['description']) && !is_string($value['description'])) {
            $errors[] = "{$prefix}.description must be a string";
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $data
     * @return list<string>
     */
    private function validateStringField(array $data, string $key, string $prefix, int $maxLength = 0): array
    {
        if (!isset($data[$key])) {
            return ["{$prefix}.{$key} is required"];
        }

        if (!is_string($data[$key])) {
            return ["{$prefix}.{$key} must be a string"];
        }

        if ($maxLength > 0 && mb_strlen($data[$key]) > $maxLength) {
            return ["{$prefix}.{$key} must not exceed {$maxLength} characters"];
        }

        return [];
    }
}
