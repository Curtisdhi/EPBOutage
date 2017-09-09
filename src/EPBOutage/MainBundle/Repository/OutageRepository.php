<?php

namespace EPBOutage\MainBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class OutageRepository extends DocumentRepository {
   
    public function findCurrentOutage() {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
                ->sort('createdOn', 'desc');
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function findLatestNearId($limit, $id) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('createdOn')
            ->field('_id')->equals($id);
        $outage = $qb->hydrate(false)->getQuery()->getSingleResult();
        $latestOutages = null;
        if ($outage) {
            $latestOutages = $this->findLatestWithIdAndCreatedDate($limit, $outage['createdOn']->toDateTime()->modify('-24 hours'));
        }
        return $latestOutages;
    }
    
    public function findLatestWithIdAndCreatedDate($limit = 1, $startDate = null) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('createdOn', 'metrics.customersAffected')
            ->limit($limit);
        $reverse = false;
        
        if (!is_null($startDate) && $startDate instanceof \DateTime) {
            $qb->field('createdOn')->gte($startDate)
                ->sort('createdOn', 'asc');
        } else {
            $qb->sort('createdOn', 'desc');
            $reverse = true;
        }
        
        $results = $qb->hydrate(false)->getQuery()->execute()->toArray();
        return !$reverse ? array_reverse($results) : $results;
    }
    
    public function findMajorOutages($minOutages) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('createdOn', 'metrics.customersAffected')
            ->field('metrics.customersAffected')->gte($minOutages);
        
        $outages = $qb->hydrate(false)->getQuery()->execute()->toArray();
        
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
