<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Outage;

class IncidentsImporter extends Importer {
    
    private Outage $outage;

    public function __construct(ManagerRegistry $doctrine, Outage $outage) {
        parent::__construct($doctrine);
        $this->outage = $outage;
        $this->object = [
            'incidents' => [],
            'districtIncidents' => [],
        ];
    }
    
    public function importFromJson(mixed $json): void {
        if (isset($json['incidents'])) {
            $this->createIncidents($json['incidents']);
        }
        if (isset($json['metrics'])) {
            $this->createDistrictIncidents($json['metrics']);
        }
        if (isset($json['restores'])) {
            $this->createIncidents($json['restores']);
            $this->outage->setStartDatetime(new \DateTimeImmutable($this->getVal('startDate', $json)));
            $this->outage->setEndDate(new \DateTimeImmutable($this->getVal('endDate', $json)));
        }
        if (isset($json['districts'])) {
            $this->createDistrictIncidents($json['districts']);
        }

        $fullJson = isset($this->outage->getFullJson()['incidents']) ? $this->outage->getFullJson()['incidents'] : [];
        $this->outage->setFullJson('incidents', array_merge($fullJson, $json));
    }
    
    private function createIncidents(mixed $incidents): void {
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
                    $this->outage->setCurrentOutages($this->outage->getCurrentOutages() + 1);
                    $this->outage->setCustomersAffected($this->outage->getCustomersAffected() + $inc['customerQty']);
                break;
                case 'SMART_GRID_RESTORE':
                    $this->outage->setAutoRestoredOutages($this->outage->getAutoRestoredOutages() + 1);

                break;
            }

            $this->outage->setCrewDispatched($this->outage->getCrewDispatched() + $inc['crewQty']);
            
        }
    }
    
    private function createDistrictIncidents(array $districtIncidents): void {
        $dis = [];
       
        foreach ($districtIncidents as $districtIncident) {
            $name = null;
            $status = null;
            $incidentQty = 0;
            $customerQty = 0;

            foreach ($districtIncident as $key => $v) {
                $this->object['districtIncidents'][$key] = [];
                if ($key === 'district') {
                    $name = $this->getVal('district', $districtIncident);
                } else {
                    $status = $key;
                    $incidentQty = $this->getVal('incidentQty', $v);
                    $customerQty = $this->getVal('customerQty', $v);
                }
            }

            if (!isset($this->object['districtIncidents'][$name])) {
                $this->object['districtIncidents'][$name] = [];
            }
            if (!isset($this->object['districtIncidents'][$name][$status])) {
                $this->object['districtIncidents'][$name][$status] = [
                    'incidentQty' => 0,
                    'customerQty' => 0,
                ];
            }
            $this->object['districtIncidents'][$name][$status]['incidentQty'] += $incidentQty;
            $this->object['districtIncidents'][$name][$status]['customerQty'] += $customerQty;

        }

    }

}