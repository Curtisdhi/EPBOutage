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
        return $qb->hydrate(false)->getQuery()->execute()->toArray();
    }
}
