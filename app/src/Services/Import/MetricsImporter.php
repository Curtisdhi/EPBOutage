<?php

// namespace App\Services\Import;

// use Doctrine\Common\Persistence\ObjectManager;
// use EPBOutage\MainBundle\Document as Document;

// class MetricsImporter extends Importer {
    
//     private $outage;
    
//     public function __construct(ObjectManager $objectManager, Document\Outage $outage) {
//         parent::__construct($objectManager);
//         $this->outage = $outage;
//     }
    
//     public function importFromJsonString($metricsJsonString) {
//         $json = json_decode($metricsJsonString, true);
        
//         $this->outage->setMetrics($this->createMetrics($json));
//         $this->outage->setFullJson('metrics', $metricsJsonString);
//     }
    
//     public function createMetrics($metrics) {
//         $docMetrics = new Document\Metrics();
        
//         $docMetrics->setBeginDtTm(strtotime($this->getVal('startDate', $metrics)));
//         $docMetrics->setEndDtTm(strtotime($this->getVal('endDate', $metrics)));
        
//         if (isset($metrics['summary']['SMART_GRID_RESTORE'])) {
//             $docMetrics->setSmartGridRestores($this->getVal('incidentQty', $metrics['summary']['SMART_GRID_RESTORE']));
//         }
//         if (isset($metrics['summary']['MANUAL_RESTORE'])) {
//             $docMetrics->setManualRestores($this->getVal('incidentQty', $metrics['summary']['MANUAL_RESTORE']));
//         }
        
//         return $docMetrics;
//     }
    

// }