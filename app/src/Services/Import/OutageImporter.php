<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;
use App\Entity\Boundaries;

class OutageImporter extends Importer {
    
    const IMPORTER_VERSION = '3.0.0';
    
    public function __construct(ManagerRegistry $doctrine) {
        parent::__construct($doctrine);
    }
    
    public function importFromJson(mixed $json): void { 
        $this->object = new Outage();
        $this->object->setImporterVersion(self::IMPORTER_VERSION);
        
        $incidentsImporter = new IncidentsImporter($this->doctrine, $this->object);
        
        $incidentsImporter->importFromJson($json['incidents']);
        $incidentsImporter->importFromJson($json['restores']);
 
    }

}