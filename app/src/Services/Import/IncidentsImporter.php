<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;

class IncidentsImporter extends Importer {
    
    private Outage $outage;

    private int $totalOutages;
    private int $totalCustomersAffected;
    
    public function __construct(ManagerRegistry $doctrine, Outage $outage) {
        parent::__construct($doctrine);
        $this->outage = $outage;
        $this->object = [
            'incidents' => [],
            'districtIncidents' => [],
        ];
    }
    
    public function importFromJson(array $json): void {
        if (isset($json['incidents'])) {
            $this->createIncidents($json['incidents']);
        }
        if (isset($json['metrics'])) {
            $this->createDistrictIncidents($json['metrics']);
        }
        if (isset($json['restores'])) {
            $this->createIncidents($json['restores']);
        }
        if (isset($json['districts'])) {
            $this->createDistrictIncidents($json['districts']);
        }

        $this->outage->setFullJson('incidents', array_merge($this->outage->getFullJson()['incidents'], $json));
    }
    
    private function createIncidents(array $incidents): void {
        $inc = [];
        foreach ($incidents as $incident) {
            $inc['crewQty'] = $this->getVal('crewQty', $incident);
            $inc['customerQty'] = $this->getVal('customerQty', $incident);
            $inc['incidentStatus'] = $this->getVal('incidentStatus', $incident);
            $inc['latitude'] = $this->getVal('latitude', $incident);
            $inc['longitude'] = $this->getVal('longitude', $incident);
            $this->object['incidents'][] = $inc;

            switch ($inc['incidentStatus']) {
                case 'OUTAGE_REPORTED':
                case 'REPAIR_IN_PROGRESS':
                    $this->totalOutages++;
                    $this->totalCustomersAffected += $inc['customerQty'];
                break;
            }
        }
    }
    
    private function createDistrictIncidents(array $districtIncidents): void {
        $dis = [];
       
        foreach ($districtIncidents as $districtIncident) {
            foreach ($districtIncident as $key => $v) {
                if ($key === 'district') {
                    $this->object['districtIncidents']['name'] = $this->getVal('district', $districtOutage);
                } else {
                    $this->object['districtIncidents']['name'][$key]['incidentQty'] += $this->getVal('incidentQty', $v);
                    $this->object['districtIncidents']['name'][$key]['customerQty'] += $this->getVal('customerQty', $v);
                }
            }
        }
    }
    
    public function getTotalOutages(): int {
        return $this->totalOutages;
    }

    public function getTotalCustomersAffected(): int {
        return $this->totalCustomersAffected;
    }
    
}