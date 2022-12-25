<?php

namespace App\Entity;

use App\Repository\OutageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OutageRepository::class)]
class Outage implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    protected $id;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private $uuid;
    
    #[ORM\Column(type: 'datetime_immutable')]
    protected $createdOn;  
    
    #[ORM\Column(type: 'string')]
    protected $importerVersion;
    
    #[ORM\Column(type: 'integer')]
    protected $autoRestoredOutages = 0;
        
    #[ORM\Column(type: 'integer')]
    protected $currentOutages = 0;

    #[ORM\Column(type: 'integer')]
    protected $customersAffected = 0;

    #[ORM\Column(type: 'integer')]
    protected $crewDispatched = 0;
            
    #[ORM\Column(type: 'integer')]
    protected $durationOutages = 0;

    #[ORM\Column(type: 'integer')]
    protected $preventedOutages = 0;

    #[ORM\Column(type: 'integer')]
    protected $totalSmartGridActivity = 0;

    #[ORM\Column(type: 'integer')]
    protected $smartGridRestores = 0;

    #[ORM\Column(type: 'integer')]
    protected $manualRestores = 0;
            
    #[ORM\Column(type: 'datetime_immutable')]
    protected $startDatetime;

    #[ORM\Column(type: 'datetime_immutable')]
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
        $this->uuid = Uuid::v7();
        $this->setCreatedOn(new \DateTimeImmutable());
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