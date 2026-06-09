<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Repository;

use Letkode\FormSchemaBundle\Domain\Entity\Form;

interface FormRepositoryInterface
{
    public function findOneByTag(string $tag): Form|null;

    public function findById(int $id): Form|null;

    public function save(Form $form, bool $flush = false): void;

    public function remove(Form $form, bool $flush = false): void;
}
