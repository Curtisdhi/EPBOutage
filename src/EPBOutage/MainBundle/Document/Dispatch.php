<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/** @Mongo\EmbeddedDocument */
class Dispatch
{
    /** @Mongo\Field(type="integer") */
    protected $id;     
    
    /** @Mongo\Field(type="integer") */
    protected $crewQty;
    
    /** @Mongo\Field(type="integer") */
    protected $customerQty;
    
    /** @Mongo\Field(type="string") */
    protected $jobStatus;
    
    /** @Mongo\Field(type="string") */
    protected $latitude;
    
    /** @Mongo\Field(type="string") */
    protected $longitude;
    
    public function __construct() {
        
    }

    public function getId() {
        return $this->id;
    }

    public function getCrewQty() {
        return $this->crewQty;
    }

    public function getCustomerQty() {
        return $this->customerQty;
    }

    public function getJobStatus() {
        return $this->jobStatus;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setCrewQty($crewQty) {
        $this->crewQty = $crewQty;
        return $this;
    }

    public function setCustomerQty($customerQty) {
        $this->customerQty = $customerQty;
        return $this;
    }

    public function setJobStatus($jobStatus) {
        $this->jobStatus = $jobStatus;
        return $this;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
        return $this;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
        return $this;
    }
    
}