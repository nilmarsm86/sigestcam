<?php

namespace App\Entity\Traits;

use App\Entity\StructuredCable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ConnectedTrait
 {
    #[ORM\OneToOne(targetEntity: StructuredCable::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?StructuredCable $structuredCable = null;

    public function getStructuredCable(): ?StructuredCable
    {
        return $this->structuredCable;
    }

    public function setStructuredCable(StructuredCable $structuredCable): static
    {
        $this->structuredCable = $structuredCable;

        return $this;
    }

 }