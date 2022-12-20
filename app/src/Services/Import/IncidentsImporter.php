<?php

namespace App\Services\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class IncidentsImporter extends Importer {
    
    private $outage;
    
    public function __construct(ObjectManager $objectManager, Document\Outage $outage) {
        parent::__construct($objectManager);
        $this->outage = $outage;
    }
    
    public function importFromJsonString($incidentsJsonString) {
        $json = json_decode($incidentsJsonString, true);
        
        if ($json) {
            $this->outage->setDispatches($this->createDispatches($json['incidents']));

            $this->outage->setDistrictOutages($this->createDistrictOutages($json['metrics']));
        }
        $this->outage->setFullJson('incidents', $incidentsJsonString);
    }
    
    public function createDispatches($dispatches) {
        $docDispatches = array();
        foreach ($dispatches as $dispatch) {
            $d = new Document\Dispatch();
            $d->setId($this->getVal('id', $dispatch));
            $d->setCrewQty($this->getVal('crewQty', $dispatch));
            $d->setCustomerQty($this->getVal('customerQty', $dispatch));
            $d->setJobStatus($this->getVal('incidentStatus', $dispatch));
            $d->setLatitude($this->getVal('latitude', $dispatch));
            $d->setLongitude($this->getVal('longitude', $dispatch));
            
            $docDispatches[] = $d;
        }
        
        return $docDispatches;
    }
    
    public function createDistrictOutages($districtOutages) {
        $docDistrictOutages = array();
       
        foreach ($districtOutages as $districtOutage) {
            $d = new Document\DistrictOutage();
            foreach ($districtOutage as $key => $v) {
                if ($key === 'district') {
                    $d->setName($this->getVal('district', $districtOutage));
                } else {
                    $d->setIncidents($this->getVal('incidentQty', $v));
                    $d->setCustomersAffected($this->getVal('customerQty', $v));
                }
            }
            
            
            $docDistrictOutages[] = $d;
        }
        return $docDistrictOutages;
    }
    
    

}