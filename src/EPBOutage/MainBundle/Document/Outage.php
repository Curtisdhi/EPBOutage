<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Mongo\Document(collection="outages", repositoryClass="EPBOutage\MainBundle\Repository\OutageRepository")
 */
class Outage implements \JsonSerializable
{
    /**
     * @Mongo\Id
     */
    protected $id;
    
     /** @Mongo\EmbedOne(targetDocument="EPBOutage\MainBundle\Document\Metrics") */
    protected $metrics;
    
    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\Dispatch") */
    protected $dispatches = array();
    
    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\DistrictOutage") */
    protected $districtOutages = array();
    
    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\Boundary") */
    protected $boundaries = array();
    
     /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable
     * @Mongo\Field(type="date")
     */
    protected $updatedOn;  
    
    /** @Mongo\Field(type="string") */
    protected $fullJson;

    public function __construct() {
        
    }

    public function getId() {
        return $this->id;
    }

    public function getMetrics() {
        return $this->metrics;
    }

    public function getDispatches() {
        return $this->dispatches;
    }
    
    public function getDistrictOutages() {
        return $this->districtOutages;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setMetrics($metrics) {
        $this->metrics = $metrics;
        return $this;
    }

    public function setDispatches($dispatches) {
        $this->dispatches = $dispatches;
        return $this;
    }

    public function setDistrictOutages($districtOutages) {
        $this->districtOutages = $districtOutages;
        return $this;
    }
    
    public function getFullJson() {
        return $this->fullJson;
    }

    public function setFullJson($fullJson) {
        $this->fullJson = $fullJson;
        return $this;
    }


    public function getBoundaries() {
        return $this->boundaries;
    }

    public function setBoundaries($boundaries) {
        $this->boundaries = $boundaries;
        return $this;
    }
    
    public function getUpdatedOn() {
        return $this->updatedOn;
    }

    public function setUpdated(\Datetime $updatedOn) {
        $this->updatedOn = $updatedOn;
        return $this;
    }

    public function jsonSerialize() {
        
        $dispatches = array();
        $districtOutages = array();
        $boundaries = array();
        foreach ($this->getDispatches() as $dispatch) {
            $dispatches[] = $dispatch->jsonSerialize();
        }
        
        foreach ($this->getDistrictOutages() as $districtOutage) {
            $districtOutages[] = $districtOutage->jsonSerialize();
        }
        
        foreach ($this->getBoundaries() as $boundary) {
            $boundaries[] = $boundary->jsonSerialize();
        }
        
        return array(
            'id' => $this->getId(),
            'metrics' => $this->getMetrics()->jsonSerialize(),
            'dispatches' => $dispatches,
            'districtOutages' => $districtOutages,
            'boundaries' => $boundaries,
        );
    }

}