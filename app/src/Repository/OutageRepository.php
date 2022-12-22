<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use App\Entity\Outage;


class OutageRepository extends ServiceEntityRepository {
   
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outage::class);
    }

    public function findCurrentOutage() {
        $qb = $this->createQueryBuilder('o')
                ->orderBy('createdOn', 'desc');
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function findWithinTimeRange($startDate, $endDate, $limit = 24) {
        $qb = $this->createQueryBuilder('o')
            ->select('o.createdOn', 'metrics.customersAffected')
            ->orderBy('o.createdOn', 'desc');
        $useLimit = true;
        
        if (!is_null($startDate) && $startDate instanceof \DateTime) {
            $qb->andWhere('o.createdOn <= :startDate')
                ->setParameter('startDate', $startDate);
        } 
        
        if (!is_null($endDate) && $endDate instanceof \DateTime) {
            $qb->andWhere('o.createdOn >= :endDate')
                ->setParameter('endDate', $endDate);
            $useLimit = false;
        } 
        
        if ($useLimit) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    public function findLatestNearId($limit, $id) {
        $qb = $this->createQueryBuilder('o')
            ->select('o.createdOn')
            ->where('_id == :id')
            ->setParameter('id', $id);
        $outage = $qb->getQuery()->getSingleResult(Query::HYDRATE_ARRAY);
        $latestOutages = null;
        if ($outage) {
            $startDate = $outage['createdOn']->toDateTime();
            $latestOutages = $this->findWithinTimeRange($startDate, (new \DateTime($startDate->format('Y-m-d H:i:sP')))->modify('-24 hours'));
        }
        return $latestOutages;
    }

    public function findMajorOutages($minOutages) {
        $qb = $this->createQueryBuilder('o')
            ->select('o.createdOn', 'metrics.customersAffected')
            ->where('metrics.customersAffected >= :minOutages')
            ->setParameter('minOutages', $minOutages);
        
        $outages = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        
        $dailyMajorOutages = array();
        foreach ($outages as $key => $outage) {
            $date = $outage['createdOn']->toDateTime()->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $d = $date->format('Y/m/d');
            
            if (!isset($dailyMajorOutages[$d]) || 
                    $dailyMajorOutages[$d]['metrics']['customersAffected'] < $outage['metrics']['customersAffected']) {
                $dailyMajorOutages[$d] = $outage;
            }
        }
        
        return array_reverse($dailyMajorOutages);
    }
}
