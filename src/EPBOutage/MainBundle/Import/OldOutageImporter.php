<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class OldOutageImporter {
    
    const IMPORTER_VERSION = '1.0.0';
    private $objectManager;
    
    private $outage;
    
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }
    
    public function importFromJsonString($outageJsonString) {
        $json = json_decode($outageJsonString, true);
        $outage = new Document\Outage();
        $this->outage = $outage;
        
        $outage->setImporterVersion(self::IMPORTER_VERSION);
        
        $m = $this->createDistrictOutages($json['districtOutages']);
        $outage->setDistrictOutages($m['districtOutages']);
        $outage->setBoundaries($m['boundaries']);
        $outage->setMetrics($this->createMetrics($json['metrics']));
        $outage->setDispatches($this->createDispatches($json['outages']));
        $outage->setFullJson($outageJsonString);
        
        $this->objectManager->persist($outage);
        $this->objectManager->flush();
    }
    
    public function rebuildFromExisting($outage) {
        $this->outage = $outage;
        $outage->setImporterVersion(self::IMPORTER_VERSION);
        
        $jsonApi = $outage->getFullJson();
        if (!is_array($jsonApi)) {
            //remember to decode because the old version didn't use a hash
            $outage->setFullJson(null, array())
                ->setFullJson('old_outages', $jsonApi);
            $jsonApi = array('old_outages' => $jsonApi);
        } 
        $json = json_decode($jsonApi['old_outages'], true);
        
        $m = $this->createDistrictOutages($json['districtOutages']);
        $outage->setDistrictOutages($m['districtOutages']);
        $outage->setBoundaries($m['boundaries']);
        $outage->setMetrics($this->createMetrics($json['metrics']));
        $outage->setDispatches($this->createDispatches($json['outages']));

        $this->objectManager->persist($outage);
        
    }
    
    public function createDistrictOutages($districtOutages) {
        $docDistrictOutages = array();
        $docBoundaries = array();
        foreach ($districtOutages as $districtOutage) {
            $d = new Document\DistrictOutage();
            $b = new Document\Boundary();
            $b->setName($this->getVal('name', $districtOutage));
            foreach ($districtOutage['boundaries'] as $bound) {
                $b->addLatLng(array(
                    'lng' => $this->getVal('longitude', $bound),
                    'lat' => $this->getVal('latitude', $bound)));
            }
            
            $d->setName($this->getVal('name', $districtOutage));
            $d->setIncidents($this->getVal('incidents', $districtOutage));
            $d->setCustomersAffected($this->getVal('customersAffected', $districtOutage));
            
            $docBoundaries[] = $b;
            $docDistrictOutages[] = $d;
        }
        
        return array('districtOutages' => $docDistrictOutages, 'boundaries' => $docBoundaries);
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
        
        $currentOutages = 0;
        $customersAffected = 0;
        foreach ($this->outage->getDistrictOutages() as $districtOutage) {
            $currentOutages += $districtOutage->getIncidents();
            $customersAffected += $districtOutage->getCustomersAffected();
        }
        
        $docMetrics->setCurrentOutages($currentOutages);
        $docMetrics->setCustomersAffected($customersAffected);
        
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