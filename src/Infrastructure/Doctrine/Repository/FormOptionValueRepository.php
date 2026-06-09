<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionValue;

/**
 * @extends ServiceEntityRepository<FormOptionValue>
 */
final class FormOptionValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormOptionValue::class);
    }

    public function save(FormOptionValue $value, bool $flush = false): void
    {
        $this->getEntityManager()->persist($value);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FormOptionValue $value, bool $flush = false): void
    {
        $this->getEntityManager()->remove($value);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
