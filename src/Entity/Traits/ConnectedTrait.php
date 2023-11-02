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

    /**
     * @return StructuredCable|null
     */
    public function getStructuredCable(): ?StructuredCable
    {
        return $this->structuredCable;
    }

    /**
     * @param StructuredCable $structuredCable
     * @return $this
     */
    public function setStructuredCable(StructuredCable $structuredCable): static
    {
        $this->structuredCable = $structuredCable;

        return $this;
    }

 }