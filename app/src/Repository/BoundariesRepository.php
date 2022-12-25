<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Boundaries;


class BoundariesRepository extends ServiceEntityRepository {
   
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boundaries::class);
    }

    public function findLatest() {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.createdOn', 'desc')
            ->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

}
