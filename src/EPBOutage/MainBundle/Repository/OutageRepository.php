<?php

namespace EPBOutage\MainBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class OutageRepository extends DocumentRepository {
   
    public function findCurrentOutage() {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
                ->sort('updatedOn', 'desc');
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function findLatestNearId($limit, $id) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('updatedOn')
            ->field('_id')->equals($id);
        $outage = $qb->hydrate(false)->getQuery()->getSingleResult();
        $latestOutages = null;
        if ($outage) {
            $latestOutages = $this->findLatestWithIdAndUpdatedDate($limit, $outage['updatedOn']->toDateTime()->modify('-24 hours'));
        }
        return $latestOutages;
    }
    
    public function findLatestWithIdAndUpdatedDate($limit = 1, $startDate = null) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('updatedOn', 'metrics.currentOutages')
            ->limit($limit);
        $reverse = false;
        
        if (!is_null($startDate) && $startDate instanceof \DateTime) {
            $qb->field('updatedOn')->gte($startDate)
                ->sort('updatedOn', 'asc');
        } else {
            $qb->sort('updatedOn', 'desc');
            $reverse = true;
        }
        
        $results = $qb->hydrate(false)->getQuery()->execute()->toArray();
        return !$reverse ? array_reverse($results) : $results;
    }
    
    public function findMajorOutages($minOutages) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('updatedOn', 'metrics.currentOutages')
            ->field('metrics.currentOutages')->gte($minOutages);
        
        $outages = $qb->hydrate(false)->getQuery()->execute()->toArray();
        
        $dailyMajorOutages = array();
        foreach ($outages as $key => $outage) {
            $date = $outage['updatedOn']->toDateTime()->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $d = $date->format('Y/m/d');
            
            if (!isset($dailyMajorOutages[$d]) || 
                    $dailyMajorOutages[$d]['metrics']['currentOutages'] < $outage['metrics']['currentOutages']) {
                $dailyMajorOutages[$d] = $outage;
            }

        }
        
        return $dailyMajorOutages;
    }
}
