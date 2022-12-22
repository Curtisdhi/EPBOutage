<?php

namespace App\Services\Import;

use Doctrine\Persistence\ManagerRegistry;

abstract class Importer {
    
    protected ManagerRegistry $doctrine;
    protected $object;
    
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function getObject(): mixed {
        return $this->object;
    }
    
    public abstract function importFromJson(array $json): void;
    
    public function flush(): void {
        $this->doctrine->getManager()->persist($this->object);
        $this->objectManager->getManager()->flush();
    }

    protected function getVal($key, $d): mixed {
        if (isset($d[$key]) && isset($d[$key]['value'])) {
            return $d[$key]['value'];
        } else if (isset($d[$key]) && !is_array($d[$key])) {
            return $d[$key];
        }
        return null;
    }
}