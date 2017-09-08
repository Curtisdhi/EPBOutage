<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class OutageImporter extends Importer {
    
    const IMPORTER_VERSION = '2.0.0';
    private $objectManager;
    
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }
    
    public function importFromJsonString($outageJsonString) { 
        
    }
    
    public function importFromJsonApiArray($jsonApi) {
        $this->object = new Document\Outage();
        
        $this->object->setImporterVersion(self::IMPORTER_VERSION);
        
        $boundariesImporter = new BoundariesImporter($this->objectManager, $this->object);
        $incidentsImporter = new IncidentsImporter($this->objectManager, $this->object);
        $metricsImporter = new MetricsImporter($this->objectManager, $this->object);
        
        $boundariesImporter->importFromJsonString($jsonApi['mobile_detail_boundaries']);
        $incidentsImporter->importFromJsonString($jsonApi['mobile_detail_incidents']);
        $metricsImporter->importFromJsonString($jsonApi['mobile_detail_restores']);

        $currentOutages = 0;
        $customersAffected = 0;
        foreach ($this->object->getDistrictOutages() as $districtOutage) {
            $currentOutages += $districtOutage->getIncidents();
            $customersAffected += $districtOutage->getCustomersAffected();
        }
        
        $this->object->getMetrics()
            ->setCurrentOutages($currentOutages)
            ->setCustomersAffected($customersAffected);
 
    }
    
    public function rebuildFromExisting($outage) {
        $outage->setImporterVersion(self::IMPORTER_VERSION);
        
        $jsonApi = $outage->getFullJson();
                
        $boundariesImporter = new BoundariesImporter($this->objectManager, $outage);
        $incidentsImporter = new IncidentsImporter($this->objectManager, $outage);
        $metricsImporter = new MetricsImporter($this->objectManager, $outage);
        
        $boundariesImporter->importFromJsonString($jsonApi['boundaries']);
        $incidentsImporter->importFromJsonString($jsonApi['incidents']);
        $metricsImporter->importFromJsonString($jsonApi['metrics']);

        $currentOutages = 0;
        $customersAffected = 0;
        foreach ($outage->getDistrictOutages() as $districtOutage) {
            $currentOutages += $districtOutage->getIncidents();
            $customersAffected += $districtOutage->getCustomersAffected();
        }
        
        $outage->getMetrics()
            ->setCurrentOutages($currentOutages)
            ->setCustomersAffected($customersAffected);
        
        $this->objectManager->persist($outage);
        
    }
    
}