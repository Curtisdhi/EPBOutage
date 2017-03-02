<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/**
 * @Mongo\Document(collection="outages", repositoryClass="EPBOutage\MainBundle\Document\OutageRepository")
 */
class Outage
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


    
}