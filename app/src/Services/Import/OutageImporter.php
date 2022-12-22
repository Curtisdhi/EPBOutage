<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;

class OutageImporter extends Importer {
    
    const IMPORTER_VERSION = '3.0.0';
    
    public function __construct(ManagerRegistry $doctrine) {
        parent::__construct($doctrine);
    }
    
    public function importFromJson(array $json): void { 
        $this->object = new Outage();
        $this->object->setImporterVersion(self::IMPORTER_VERSION);
        
        $boundariesImporter = new BoundariesImporter($this->doctrine, $this->object);
        $incidentsImporter = new IncidentsImporter($this->doctrine, $this->object);
        
        $boundariesImporter->importFromJsonString($json['mobile_detail_boundaries']);
        $incidentsImporter->importFromJsonString($json['mobile_detail_incidents']);
        $incidentsImporter->importFromJsonString($json['mobile_detail_restores']);

        $this->object
            ->setCurrentOutages($incidentsImporter->getTotalOutages())
            ->setCustomersAffected($incidentsImporter->getTotalCustomersAffected());
 
    }

}