<?php

namespace EPBOutage\MainBundle\Import;

use Doctrine\Common\Persistence\ObjectManager;

abstract class Importer {
    
    protected $objectManager;
    protected $object;
    
    public function __construct(ObjectManager $objectManager) {
        $this->objectManager = $objectManager;
    }
    
    public function getObject() {
        return $this->object;
    }
    
    public abstract function importFromJsonString($outageJsonString);
    
    public function flush() {
        $this->objectManager->persist($this->object);
        $this->objectManager->flush();
    }

    protected function getVal($key, $d) {
        if (isset($d[$key]) && isset($d[$key]['value'])) {
            return $d[$key]['value'];
        } else if (isset($d[$key]) && !is_array($d[$key])) {
            return $d[$key];
        }
        return null;
    }
}