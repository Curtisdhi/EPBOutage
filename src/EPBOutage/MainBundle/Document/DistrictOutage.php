<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/** @Mongo\EmbeddedDocument */
class DistrictOutage
{
    /** @Mongo\Field(type="string") */
    protected $name;
    
    /** @Mongo\Field(type="integer") */
    protected $incidents;
    
    /** @Mongo\Field(type="integer") */
    protected $customersAffected;
    
    /** @Mongo\Field(type="collection") */
    protected $latitudes = array();
    
    /** @Mongo\Field(type="collection") */
    protected $longitudes = array();
    
    public function __construct() {
        
    }
    
    public function getName() {
        return $this->name;
    }

    public function getIncidents() {
        return $this->incidents;
    }

    public function getCustomersAffected() {
        return $this->customersAffected;
    }

    public function getLatitudes() {
        return $this->latitudes;
    }

    public function getLongitudes() {
        return $this->longitudes;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setIncidents($incidents) {
        $this->incidents = $incidents;
        return $this;
    }

    public function setCustomersAffected($customersAffected) {
        $this->customersAffected = $customersAffected;
        return $this;
    }

    public function setLatitudes($latitudes) {
        $this->latitudes = $latitudes;
        return $this;
    }

    public function setLongitudes($longitudes) {
        $this->longitudes = $longitudes;
        return $this;
    }
    
    public function addLatitude($latitude) {
        $this->latitudes[] = $latitude;
        return $this;
    }
    
    public function addLongitude($longitude) {
        $this->longitudes[] = $longitude;
        return $this;
    }


}