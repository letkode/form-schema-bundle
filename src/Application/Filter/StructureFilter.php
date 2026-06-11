<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\Filter;

use Letkode\FormSchemaBundle\Domain\Entity\FormGroup;
use Letkode\FormSchemaBundle\Domain\Entity\FormSection;

final readonly class StructureFilter
{
    public function __construct(private FilterCriteria $criteria)
    {
    }

    /**
     * @param iterable<int, FormSection> $sections
     *
     * @return list<FormSection>
     */
    public function filterSections(iterable $sections): array
    {
        $result = [];
        foreach ($sections as $section) {
            if ($section instanceof FormSection && $this->criteria->isSectionAllowed($section->tag)) {
                $result[] = $section;
            }
        }

        return $result;
    }

    /**
     * @param iterable<int, FormGroup> $groups
     *
     * @return list<FormGroup>
     */
    public function filterGroups(iterable $groups): array
    {
        $result = [];
        foreach ($groups as $group) {
            if ($group instanceof FormGroup && $this->criteria->isGroupAllowed($group->tag)) {
                $result[] = $group;
            }
        }

        return $result;
    }
}
