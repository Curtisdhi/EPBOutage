<?php

namespace App\Entity;

use App\Repository\OutageRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: OutageRepository::class)]
class Outage implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected $id;
    
    #[ORM\Column(type: 'string')]
    protected $importerVersion;
    
    /** @Mongo\Field(type="integer") */
    protected $autoRestoredOutages;
        
    /** @Mongo\Field(type="integer") */
    protected $currentOutages;

    /** @Mongo\Field(type="integer") */
    protected $customersAffected;

    /** @Mongo\Field(type="integer") */
    protected $crewDispatched;
            
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

    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\Dispatch") */
    protected $dispatches = array();
    
    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\DistrictOutage") */
    protected $districtOutages = array();
    
    /** @Mongo\EmbedMany(targetDocument="EPBOutage\MainBundle\Document\Boundary") */
    protected $boundaries = array();
    
     /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @Mongo\Field(type="date")
     */
    protected $updatedOn;  
    
    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="create")
     * @Mongo\Field(type="date")
     */
    protected $createdOn;  
    
    /** @Mongo\Field(type="hash") */
    protected $fullJson = array();

    public function __construct() {
        
    }
    
    function getImporterVersion() {
        return $this->importerVersion;
    }

    function setImporterVersion($importerVersion) {
        $this->importerVersion = $importerVersion;
    }

    public function getId() {
        return $this->id;
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
    
    function getCustomersAffected() {
        return $this->customersAffected;
    }

    function setCustomersAffected($customersAffected) {
        $this->customersAffected = $customersAffected;
    }
    
    function getCrewDispatched() {
        return $this->crewDispatched;
    }

    function setCrewDispatched($crewDispatched) {
        $this->crewDispatched = $crewDispatched;
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

    public function setFullJson($key, $fullJson) {
        if ($key === null) {
            $this->fullJson = $fullJson;
        } else {
            $this->fullJson[$key] = $fullJson;
        }
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
    
    function getCreatedOn() {
        return $this->createdOn;
    }

    function setCreatedOn(\Datetime $createdOn) {
        $this->createdOn = $createdOn;
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
            'metrics' => [
                'autoRestoredOutages' => $this->getAutoRestoredOutages(),
                'currentOutages' => $this->getCurrentOutages(),
                'customersAffected' => $this->getCustomersAffected(),
                'crewDispatched' => $this->getCrewDispatched(),
                'durationOutages' => $this->getDurationOutages(),
                'preventedOutages' => $this->getPreventedOutages(),
                'smartGridRestores' => $this->getSmartGridRestores(),
                'manualRestores' => $this->getManualRestores(),
                'totalSmartGridActivity' => $this->getTotalSmartGridActivity(),
                'beginDtTm' => $this->getBeginDtTm(),
                'endDtTm' => $this->getEndDtTm(),
            ],
            'dispatches' => $dispatches,
            'districtOutages' => $districtOutages,
            'boundaries' => $boundaries,
        );
    }

}