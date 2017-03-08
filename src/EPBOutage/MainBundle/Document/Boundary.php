<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/** @Mongo\EmbeddedDocument */
class Boundary implements \JsonSerializable
{
    /** @Mongo\Field(type="string") */
    protected $name;
    
    /** @Mongo\Field(type="collection") */
    protected $latlng = array();
    
    public function __construct() {
        
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getLatlng() {
        return $this->latlng;
    }

    public function setLatlng($latlng) {
        $this->latlng = $latlng;
        return $this;
    }
    
    public function addLatLng($latLng) {
        $this->latlng[] = $latLng;
        return $this;
    }

    public function jsonSerialize() {
        return array(
            'name' => $this->getName(),
            'latLng' => $this->getLatlng(),
        );
    }

}