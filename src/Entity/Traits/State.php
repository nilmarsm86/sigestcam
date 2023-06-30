<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enums\State as StateEnum;

trait State
 {
    #[ORM\Column(length: 255)]
    protected string $state;
    protected StateEnum $enumState;

    public function getState(): StateEnum
    {
        return $this->enumState;
    }

    public function setState(StateEnum $state): static
    {
        $this->enumState = $state;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->state = $this->getState()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(StateEnum::from($this->state));
    }
 }