<?php

namespace EPBOutage\MainBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class OutageRepository extends DocumentRepository {
   
    public function findCurrentOutage() {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
                ->sort('updatedOn', 'desc');
        
        return $qb->getQuery()->getSingleResult();
    }
    
    public function findLatestWithIdAndUpdatedDate($limit = 1) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('updatedOn')
            ->sort('updatedOn', 'desc')
            ->limit($limit);
        return array_reverse($qb->hydrate(false)->getQuery()->execute()->toArray());
    }
    
    public function findMajorOutages($minOutages) {
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:Outage')
            ->select('updatedOn', 'metrics.currentOutages')
            ->sort(array('updatedOn' => 'desc', 'metrics.currentOutages' => 'desc'))
            ->field('metrics.currentOutages')->gte($minOutages);
        
        $outages = $qb->hydrate(false)->getQuery()->execute()->toArray();
        
        $date = null;
        foreach ($outages as $key => $outage) {
            $newDate = $outage['updatedOn']->toDateTime();
            if (!is_null($date) && $date->format('Y/m/d') === $newDate->format('Y/m/d')) {
                unset($outages[$key]);
            } else {
                $date = $newDate;
            }
        }
        
        return $outages;
    }
}
