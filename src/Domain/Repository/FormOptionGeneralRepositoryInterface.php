<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Repository;

use Letkode\FormSchemaBundle\Domain\Entity\FormOptionGeneral;

interface FormOptionGeneralRepositoryInterface
{
    public function findOneByTag(string $tag): FormOptionGeneral|null;

    public function findById(int $id): FormOptionGeneral|null;

    public function save(FormOptionGeneral $optionGeneral, bool $flush = false): void;

    public function remove(FormOptionGeneral $optionGeneral, bool $flush = false): void;
}
