<?php

namespace EPBOutage\MainBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class AlertUserRepository extends DocumentRepository {
       
    public function findByWithinThresholdDelay($customersAffected, $thresholdDelay) {
        $now = new \DateTime();
        $timeAtDelay = $now->sub(new \DateInterval($thresholdDelay));
        
        $qb = $this->dm->createQueryBuilder('EPBOutageMainBundle:AlertUser')
            ->field('customersAffectedThreshold')->lt($customersAffected)
            ->field('lastAlertSent')->lt($timeAtDelay);
                   
        return $qb->getQuery()->execute();
    }

}
