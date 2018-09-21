<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Mongo\Document(collection="alertUsers", repositoryClass="EPBOutage\MainBundle\Repository\AlertUserRepository")
 */
class AlertUser {
    
    /**
     * @Mongo\Id
     */
    protected $id;
    
    /** @Mongo\Field(type="string") */
    protected $email;
    
    /** @Mongo\Field(type="integer") */
    protected $customersAffectedThreshold;
    
    /** 
     * @Gedmo\Timestampable(on="create")
     * @Mongo\Field(type="date") */
    protected $lastAlertSent;
    
    public function __construct() {
        
    }
    
    function getId() {
        return $this->id;
    }

    function getEmail() {
        return $this->email;
    }

    function getCustomersAffectedThreshold() {
        return $this->customersAffectedThreshold;
    }

    function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    function setCustomersAffectedThreshold($customersAffectedThreshold) {
        $this->customersAffectedThreshold = $customersAffectedThreshold;
        return $this;
    }
    
    function getLastAlertSent() {
        return $this->lastAlertSent;
    }

    function setLastAlertSent($lastAlertSent) {
        $this->lastAlertSent = $lastAlertSent;
        return $this;
    }



}