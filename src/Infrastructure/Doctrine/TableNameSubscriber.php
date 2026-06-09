<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Doctrine;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Entity\FormField;
use Letkode\FormSchemaBundle\Domain\Entity\FormGroup;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneral;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneralValue;
use Letkode\FormSchemaBundle\Domain\Entity\FormSection;

final class TableNameSubscriber
{
    /** @var array<class-string, string> */
    private const DEFAULT_TABLES = [
        Form::class => 'form',
        FormSection::class => 'form_section',
        FormGroup::class => 'form_group',
        FormField::class => 'form_field',
        FormOptionGeneral::class => 'form_option_general',
        FormOptionGeneralValue::class => 'form_option_general_value',
    ];

    /** @var array<class-string, string> */
    private array $resolvedNames;

    /**
     * @param array<string, string|null> $names
     */
    public function __construct(string $prefix, array $names)
    {
        $this->resolvedNames = [];

        foreach (self::DEFAULT_TABLES as $class => $default) {
            $key = $this->toKey($class);
            $this->resolvedNames[$class] = $names[$key] ?? ($prefix . $default);
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $meta = $args->getClassMetadata();

        if (!isset($this->resolvedNames[$meta->getName()])) {
            return;
        }

        $meta->setPrimaryTable(['name' => $this->resolvedNames[$meta->getName()]]);
    }

    private function toKey(string $fqcn): string
    {
        $short = substr($fqcn, strrpos($fqcn, '\\') + 1);

        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $short));
    }
}
