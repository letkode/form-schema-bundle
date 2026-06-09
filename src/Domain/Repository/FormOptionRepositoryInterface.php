<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Repository;

use Letkode\FormSchemaBundle\Domain\Entity\FormOption;

interface FormOptionRepositoryInterface
{
    public function findOneByTag(string $tag): FormOption|null;

    public function findById(int $id): FormOption|null;

    public function save(FormOption $option, bool $flush = false): void;

    public function remove(FormOption $option, bool $flush = false): void;
}
