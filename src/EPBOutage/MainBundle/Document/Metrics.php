<?php

namespace EPBOutage\MainBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;

/** @Mongo\EmbeddedDocument */
class Metrics implements \JsonSerializable
{
   
    /** @Mongo\Field(type="integer") */
    protected $autoRestoredOutages;
    
    /** @Mongo\Field(type="integer") */
    protected $currentOutages;
            
    /** @Mongo\Field(type="integer") */
    protected $durationOutages;
    
    /** @Mongo\Field(type="integer") */
    protected $preventedOutages;
    
    /** @Mongo\Field(type="integer") */
    protected $totalSmartGridActivity;
    
    /** @Mongo\Field(type="integer") */
    protected $smartGridRestores;
    
    /** @Mongo\Field(type="integer") */
    protected $manualRestores;
            
    /** @Mongo\Field(type="integer") */
    protected $beginDtTm;
    
    /** @Mongo\Field(type="integer") */
    protected $endDtTm;
    
    public function __construct() {
        
    }

    public function getAutoRestoredOutages() {
        return $this->autoRestoredOutages;
    }

    public function getCurrentOutages() {
        return $this->currentOutages;
    }

    public function getDurationOutages() {
        return $this->durationOutages;
    }

    public function getPreventedOutages() {
        return $this->preventedOutages;
    }
    
    function getSmartGridRestores() {
        return $this->smartGridRestores;
    }
    
    function getManualRestores() {
        return $this->manualRestores;
    }
    
    public function getTotalSmartGridActivity() {
        return $this->totalSmartGridActivity;
    }

    public function getBeginDtTm() {
        return $this->beginDtTm;
    }

    public function getEndDtTm() {
        return $this->endDtTm;
    }

    public function setAutoRestoredOutages($autoRestoredOutages) {
        $this->autoRestoredOutages = $autoRestoredOutages;
        return $this;
    }

    public function setCurrentOutages($currentOutages) {
        $this->currentOutages = $currentOutages;
        return $this;
    }

    public function setDurationOutages($durationOutages) {
        $this->durationOutages = $durationOutages;
        return $this;
    }

    public function setPreventedOutages($preventedOutages) {
        $this->preventedOutages = $preventedOutages;
        return $this;
    }

    function setSmartGridRestores($smartGridRestores) {
        $this->smartGridRestores = $smartGridRestores;
    }

    function setManualRestores($manualRestores) {
        $this->manualRestores = $manualRestores;
    }

    public function setTotalSmartGridActivity($totalSmartGridActivity) {
        $this->totalSmartGridActivity = $totalSmartGridActivity;
        return $this;
    }

    public function setBeginDtTm($beginDtTm) {
        $this->beginDtTm = $beginDtTm;
        return $this;
    }

    public function setEndDtTm($endDtTm) {
        $this->endDtTm = $endDtTm;
        return $this;
    }

    public function jsonSerialize() {
        return array(
            'autoRestoredOutages' => $this->getAutoRestoredOutages(),
            'currentOutages' => $this->getCurrentOutages(),
            'durationOutages' => $this->getDurationOutages(),
            'preventedOutages' => $this->getPreventedOutages(),
            'smartGridRestores' => $this->getSmartGridRestores(),
            'manualRestores' => $this->getManualRestores(),
            'totalSmartGridActivity' => $this->getTotalSmartGridActivity(),
            'beginDtTm' => $this->getBeginDtTm(),
            'endDtTm' => $this->getEndDtTm(),
        );
    }

}