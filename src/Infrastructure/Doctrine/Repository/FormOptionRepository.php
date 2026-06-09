<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Letkode\FormSchemaBundle\Domain\Entity\FormOption;
use Letkode\FormSchemaBundle\Domain\Repository\FormOptionRepositoryInterface;

/**
 * @extends ServiceEntityRepository<FormOption>
 */
final class FormOptionRepository extends ServiceEntityRepository implements FormOptionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormOption::class);
    }

    #[\Override]
    public function findOneByTag(string $tag): FormOption|null
    {
        return $this->findOneBy(['tag' => $tag]);
    }

    #[\Override]
    public function findById(int $id): FormOption|null
    {
        return $this->find($id);
    }

    #[\Override]
    public function save(FormOption $option, bool $flush = false): void
    {
        $this->getEntityManager()->persist($option);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[\Override]
    public function remove(FormOption $option, bool $flush = false): void
    {
        $this->getEntityManager()->remove($option);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
