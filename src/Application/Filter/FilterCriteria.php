<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Application\Filter;

final readonly class FilterCriteria
{
    /**
     * @param array<string> $includeSections
     * @param array<string> $excludeSections
     * @param array<string> $includeGroups
     * @param array<string> $excludeGroups
     */
    public function __construct(
        public array $includeSections = [],
        public array $excludeSections = [],
        public array $includeGroups = [],
        public array $excludeGroups = [],
    ) {
    }

    public function isSectionAllowed(string $tag): bool
    {
        if (!empty($this->includeSections)) {
            return \in_array($tag, $this->includeSections, true);
        }

        return !\in_array($tag, $this->excludeSections, true);
    }

    public function isGroupAllowed(string $tag): bool
    {
        if (!empty($this->includeGroups)) {
            return \in_array($tag, $this->includeGroups, true);
        }

        return !\in_array($tag, $this->excludeGroups, true);
    }
}
