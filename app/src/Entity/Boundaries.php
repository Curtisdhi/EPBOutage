<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BoundariesRepository;

#[ORM\Entity(repositoryClass: BoundariesRepository::class)]
class Boundaries
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]    
    protected $id;
    
    #[ORM\Column(type: 'string')]
    protected $hashSum;
    
    #[ORM\Column(type: 'json')]
    protected $boundariesJson;
    
    public function __construct() {
        $this->boundariesJson = [];
    }

    public function getId(): int {
        return $this->id;
    }
    
    public function setHashSum(string $hashSum): self {
        $this->hashSum = $hashSum;
        return $this;
    }

    public function getHashSum(): string {
        return $this->hashSum;
    }

    public function setBoundariesJson(array $boundariesJson): self {
        $this->boundariesJson = $boundariesJson;
        return $this;
    }

    public function getBoundariesJson(): array {
        return $this->boundariesJson;
    }

}