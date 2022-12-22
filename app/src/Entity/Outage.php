<?php

namespace App\Entity;

use App\Repository\OutageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: OutageRepository::class)]
class Outage implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    protected $id;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private $uuid;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'date_immutable')]
    protected $updatedOn;  
    
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'date_immutable')]
    protected $createdOn;  
    
    #[ORM\Column(type: 'string')]
    protected $importerVersion;
    
    #[ORM\Column(type: 'integer')]
    protected $autoRestoredOutages;
        
    #[ORM\Column(type: 'integer')]
    protected $currentOutages;

    #[ORM\Column(type: 'integer')]
    protected $customersAffected;

    #[ORM\Column(type: 'integer')]
    protected $crewDispatched;
            
    #[ORM\Column(type: 'integer')]
    protected $durationOutages;

    #[ORM\Column(type: 'integer')]
    protected $preventedOutages;

    #[ORM\Column(type: 'integer')]
    protected $totalSmartGridActivity;

    #[ORM\Column(type: 'integer')]
    protected $smartGridRestores;

    #[ORM\Column(type: 'integer')]
    protected $manualRestores;
            
    #[ORM\Column(type: 'date_immutable')]
    protected $startDatetime;

    #[ORM\Column(type: 'date_immutable')]
    protected $endDateTime;    
    
    #[ORM\ManyToOne(targetEntity: Boundaries::class)]
    protected $boundaries;

    #[ORM\Column(type: 'json')]
    protected $incidents;

    #[ORM\Column(type: 'json')]
    protected $districtIncidents;

    #[ORM\Column(type: 'json')]
    protected $fullJson;

    public function __construct() {
        $this->incidents = [];
        $this->districtIncidents = [];
        $this->fullJson = [];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUuid(): Uuid {
        return $this->uuid;
    }

    public function getUpdatedOn(): \DateTimeImmutable {
        return $this->updatedOn;
    }

    public function setUpdated(\DateTimeImmutable $updatedOn): self {
        $this->updatedOn = $updatedOn;
        return $this;
    }
    
    public function getCreatedOn(): \DateTimeImmutable {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeImmutable $createdOn): self {
        $this->createdOn = $createdOn;
        return $this;
    }

    function getImporterVersion(): string {
        return $this->importerVersion;
    }

    function setImporterVersion($importerVersion): self {
        $this->importerVersion = $importerVersion;
        return $this;
    }

    public function getAutoRestoredOutages(): int {
        return $this->autoRestoredOutages;
    }
    
    public function setAutoRestoredOutages($autoRestoredOutages): self {
        $this->autoRestoredOutages = $autoRestoredOutages;
        return $this;
    }

    public function getCurrentOutages(): int {
        return $this->currentOutages;
    }

    public function setCurrentOutages($currentOutages): self {
        $this->currentOutages = $currentOutages;
        return $this;
    }

    public function getDurationOutages(): int {
        return $this->durationOutages;
    }

    public function setDurationOutages($durationOutages): self {
        $this->durationOutages = $durationOutages;
        return $this;
    }

    public function getPreventedOutages(): int {
        return $this->preventedOutages;
    }
    
    public function setPreventedOutages($preventedOutages): self {
        $this->preventedOutages = $preventedOutages;
        return $this;
    }
    
    function getSmartGridRestores(): int {
        return $this->smartGridRestores;
    }
    
    function setSmartGridRestores($smartGridRestores): self {
        $this->smartGridRestores = $smartGridRestores;
        return $this;
    }

    function getManualRestores(): int {
        return $this->manualRestores;
    }

    function setManualRestores($manualRestores): self {
        $this->manualRestores = $manualRestores;
        return $this;
    }

    public function getTotalSmartGridActivity(): int {
        return $this->totalSmartGridActivity;
    }

    public function setTotalSmartGridActivity($totalSmartGridActivity): self {
        $this->totalSmartGridActivity = $totalSmartGridActivity;
        return $this;
    }

    public function setStartDatetime($startDatetime): self {
        $this->startDatetime = $startDatetime;
        return $this;
    }

    public function getStartDatetime(): \DateTimeImmutable {
        return $this->startDatetime;
    }

    public function setEndDate($endDateTime): self {
        $this->endDateTime = $endDateTime;
        return $this;
    }

    public function getEndDatetime(): \DateTimeImmutable {
        return $this->endDateTime;
    }
    
    public function getCustomersAffected(): int {
        return $this->customersAffected;
    }

    public function setCustomersAffected(int $customersAffected): self {
        $this->customersAffected = $customersAffected;
        return $this;
    }
    
    public function getCrewDispatched(): int {
        return $this->crewDispatched;
    }

    public function setCrewDispatched(int $crewDispatched): self {
        $this->crewDispatched = $crewDispatched;
        return $this;
    }
    
    public function getFullJson(): array {
        return $this->fullJson;
    }

    public function setFullJson($key, $fullJson): self {
        if ($key === null) {
            $this->fullJson = $fullJson;
        } else {
            $this->fullJson[$key] = $fullJson;
        }
        return $this;
    }

    public function getBoundaries(): ?Boundaries {
        return $this->boundaries;
    }

    public function setBoundaries(?Boundaries $boundaries): self {
        $this->boundaries = $boundaries;
        return $this;
    }
    
    public function jsonSerialize(): mixed {
        return [
            'id' => $this->getUuid(),
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
                'startDatetime' => $this->getStartDatetime(),
                'endDatetime' => $this->getEndDatetime(),
            ],
            'incidents' => $this->incidents,
            'districtIncidents' => $this->districtIncidents,
            'boundaries' => $this->boundaries->getBoundariesJson(),
        ];
    }

}