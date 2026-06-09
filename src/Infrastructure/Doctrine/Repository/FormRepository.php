<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Repository\FormRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Form>
 */
final class FormRepository extends ServiceEntityRepository implements FormRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Form::class);
    }

    #[\Override]
    public function findOneByTag(string $tag): Form|null
    {
        return $this->findOneBy(['tag' => $tag]);
    }

    #[\Override]
    public function findById(int $id): Form|null
    {
        return $this->find($id);
    }

    #[\Override]
    public function save(Form $form, bool $flush = false): void
    {
        $this->getEntityManager()->persist($form);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function remove(Form $form, bool $flush = false): void
    {
        $this->getEntityManager()->remove($form);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
