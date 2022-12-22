<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;
use App\Entity\Boundaries;

class BoundariesImporter extends Importer {
    
    private Outage $outage;
    
    public function __construct(ManagerRegistry $doctrine, Outage $outage) {
        parent::__construct($doctrine);
        $this->outage = $outage;
    }
    
    public function importFromJson(array $json): void {
        $entityManager = $this->doctrine->getManager();

        $hashSum = hash('sha256', $json['districts'], false);

        $boundaries = $entityManager->getRepository(Boundaries::class)
            ->findOneBy(['hashSum' => $hashSum]);

        if ($boundaries === null) {
            $this->outage->setBoundaries($this->createBoundaries($json['districts']));
        } else {
            $this->outage->setBoundaries($boundaries);
        }

        $this->outage->setFullJson('boundaries', $json);
    }
    
    private function createBoundaries(array $boundaries): Boundaries  {
        $boundaries = new Boundaries();
        
        foreach ($boundaries as $boundary) {
            $b = [];
            $b['name'] = $this->getVal('boundaryName', $boundary);
            $b['coordinates'] = [];
            foreach ($boundary['coordinates'] as $coord) {
                $b['coordinates'][] = [
                    'longitude' => $this->getVal('longitude', $coord),
                    'latitude' => $this->getVal('latitude', $coord),
                ];
            }
           
            $boundaries[] = $b;
        }
        
        return $boundaries;
    }
    

}