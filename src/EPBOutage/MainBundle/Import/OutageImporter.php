<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class OutageImporter {
    
    private $objectManager;
    
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }
    
    public function importFromJsonString($outageJsonString) {
        $json = json_decode($outageJsonString, true);
        $outage = new Document\Outage();
        $outage->setDistrictOutages($this->createDistrictOutages($json['districtOutages']));
        $outage->setMetrics($this->createMetrics($json['metrics']));
        $outage->setDispatches($this->createDispatches($json['outages']));
        $outage->setFullJson($outageJsonString);
        
        $this->objectManager->persist($outage);
        $this->objectManager->flush();
    }
    
    public function createDistrictOutages($districtOutages) {
        $docBoundaries = array();
        foreach ($districtOutages as $districtOutage) {
            $b = new Document\DistrictOutage();
            foreach ($districtOutage['boundaries'] as $bound) {
                $b->addLongitude($this->getVal('longitude', $bound));
                $b->addLatitude($this->getVal('longitude', $bound));
            }
            $b->setName($this->getVal('name', $districtOutage));
            $b->setIncidents($this->getVal('incidents', $districtOutage));
            $b->setCustomersAffected($this->getVal('customersAffected', $districtOutage));
            
            $docBoundaries[] = $b;
        }
        
        return $docBoundaries;
    }
    
    public function createMetrics($metrics) {
        $docMetrics = new Document\Metrics();
       
        $docMetrics->setAutoRestoredOutages($this->getVal('autoRestoredOutages', $metrics));
        $docMetrics->setBeginDtTm($this->getVal('beginDtTm', $metrics));
        $docMetrics->setEndDtTm($this->getVal('endDtTm', $metrics));
        $docMetrics->setCurrentOutages($this->getVal('currentOutages', $metrics));
        $docMetrics->setDurationOutages($this->getVal('durationOutages', $metrics));
        $docMetrics->setPreventedOutages($this->getVal('preventedOutages', $metrics));
        $docMetrics->setTotalSmartGridActivity($this->getVal('totalSmartGridActivity', $metrics));
        
        return $docMetrics;
    }
    
    
    public function createDispatches($dispatches) {
        $docDispatches = array();
        foreach ($dispatches as $dispatch) {
            $d = new Document\Dispatch();
            $d->setId($this->getVal('id', $dispatch));
            $d->setCrewQty($this->getVal('crewQty', $dispatch));
            $d->setCustomerQty($this->getVal('customerQty', $dispatch));
            $d->setJobStatus($this->getVal('jobStatus', $dispatch));
            $d->setLatitude($this->getVal('latitude', $dispatch));
            $d->setLongitude($this->getVal('longitude', $dispatch));
            
            $docDispatches[] = $d;
        }
        
        return $docDispatches;
    }
    
    private function getVal($key, $d) {
        if (isset($d[$key]) && isset($d[$key]['value'])) {
            return $d[$key]['value'];
        } else if (isset($d[$key]) && !is_array($d[$key])) {
            return $d[$key];
        }
        return null;
    }
}