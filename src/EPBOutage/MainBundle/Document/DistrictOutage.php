<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/** @Mongo\EmbeddedDocument */
class DistrictOutage implements \JsonSerializable
{
    /** @Mongo\Field(type="string") */
    protected $name;
    
    /** @Mongo\Field(type="integer") */
    protected $incidents;
    
    /** @Mongo\Field(type="integer") */
    protected $customersAffected;
    
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

    public function jsonSerialize() {
        return array(
            'name' => $this->getName(),
            'incidents' => $this->getIncidents(),
            'customersAffected' => $this->getCustomersAffected(),
        );
    }

}