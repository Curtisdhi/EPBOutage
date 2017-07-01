<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;
use EPBOutage\MainBundle\Document as Document;

class BoundariesImporter extends Importer {
    
    private $objectManager;
    private $outage;
    
    public function __construct(ObjectManager $objectManager, Document\Outage $outage) {
        $this->outage = $outage;
    }
    
    public function importFromJsonString($boundariesJsonString) {
        $json = json_decode($boundariesJsonString, true);
        $this->outage->setBoundaries($this->createBoundaries($json['districts']));
        $this->outage->setFullJson('boundaries', $boundariesJsonString);
    }
    
    public function createBoundaries($boundaries) {
        $docBoundaries = array();
        foreach ($boundaries as $boundary) {
            $b = new Document\Boundary();
            $b->setName($this->getVal('boundaryName', $boundary));
            foreach ($boundary['coordinates'] as $coord) {
                $b->addLatLng(array(
                    'lng' => $this->getVal('longitude', $coord),
                    'lat' => $this->getVal('latitude', $coord)));
            }
           
            $docBoundaries[] = $b;
        }
        
        return $docBoundaries;
    }
    

}