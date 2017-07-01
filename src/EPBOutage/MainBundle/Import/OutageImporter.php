<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class OutageImporter {
    
    private $objectManager;
    
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }
    
    public function importFromJsonApiArray($jsonApi) {
        $outage = new Document\Outage();
        
        $boundariesImporter = new BoundariesImporter($this->objectManager, $outage);
        $incidentsImporter = new IncidentsImporter($this->objectManager, $outage);
        $metricsImporter = new MetricsImporter($this->objectManager, $outage);
        
        $boundariesImporter->importFromJsonString($jsonApi['mobile_detail_boundaries']);
        $incidentsImporter->importFromJsonString($jsonApi['mobile_detail_incidents']);
        $metricsImporter->importFromJsonString($jsonApi['mobile_detail_restores']);

        $currentOutages = 0;
        foreach ($outage->getDistrictOutages() as $districtOutage) {
            $currentOutages += $districtOutage->getIncidents();
        }
        
        $outage->getMetrics()->setCurrentOutages($currentOutages);
        
        $this->objectManager->persist($outage);
        $this->objectManager->flush();
    }
    
}