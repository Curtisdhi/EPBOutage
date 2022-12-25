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
    
    public abstract function importFromJson(mixed $json): void;
    
    public function persist(): void {
        $this->doctrine->getManager()->persist($this->object);
    }

    public function flush(): void {
        $this->persist();
        $this->doctrine->getManager()->flush();
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