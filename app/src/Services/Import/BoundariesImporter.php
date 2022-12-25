<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;
use App\Entity\Boundaries;

class BoundariesImporter extends Importer {

    public function __construct(ManagerRegistry $doctrine) {
        parent::__construct($doctrine);
    }
    
    public function importFromJson(mixed $json): void {
        $entityManager = $this->doctrine->getManager();

        $hashSum = hash('sha256', serialize($json['districts']), false);

        $this->object = $entityManager->getRepository(Boundaries::class)
            ->findOneBy(['hashSum' => $hashSum]);

        if ($this->object === null) {
            $this->object = $this->createBoundaries($json['districts']);
            $this->object->setHashSum($hashSum);
        }

    }
    
    private function createBoundaries(mixed $jsonBoundaries): Boundaries  {
        $boundaries = new Boundaries();
        
        $newJsonBoundaries = [];
        foreach ($jsonBoundaries as $boundary) {
            $b = [];
            $b['name'] = $this->getVal('boundaryName', $boundary);
            $b['coordinates'] = [];
            foreach ($boundary['coordinates'] as $coord) {
                $b['coordinates'][] = [
                    'longitude' => $this->getVal('longitude', $coord),
                    'latitude' => $this->getVal('latitude', $coord),
                ];
            }
           
            $newJsonBoundaries[] = $b;
        }
        
        $boundaries->setBoundariesJson($newJsonBoundaries);
        
        return $boundaries;
    }
    
}