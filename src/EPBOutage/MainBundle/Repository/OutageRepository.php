<?php

namespace EPBOutage\MainBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class OutageRepository extends DocumentRepository {
   
    public function findCurrentOutage() {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
                ->sort('createdOn', 'desc');
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function findWithinTimeRange($startDate, $endDate, $limit = 24) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('createdOn', 'metrics.customersAffected')
            ->sort('createdOn', 'desc');
        $useLimit = true;
        
        if (!is_null($startDate) && $startDate instanceof \DateTime) {
            $qb->field('createdOn')->lte($startDate);
        } 
        
        if (!is_null($endDate) && $endDate instanceof \DateTime) {
            $qb->field('createdOn')->gte($endDate);
            $useLimit = false;
        } 
        
        if ($useLimit) {
            $qb->limit($limit);
        }
        
        return $qb->hydrate(false)->getQuery()->execute()->toArray();
    }
    
    public function findLatestNearId($limit, $id) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('createdOn')
            ->field('_id')->equals($id);
        $outage = $qb->hydrate(false)->getQuery()->getSingleResult();
        $latestOutages = null;
        if ($outage) {
            $startDate = $outage['createdOn']->toDateTime();
            $latestOutages = $this->findWithinTimeRange($startDate, (new \DateTime($startDate->format('Y-m-d H:i:sP')))->modify('-24 hours'));
        }
        return $latestOutages;
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
