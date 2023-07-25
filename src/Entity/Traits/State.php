<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enums\State as StateEnum;
use Symfony\Component\Validator\Constraints as Assert;

trait State
 {
    #[ORM\Column]
    protected ?int $state;

    #[Assert\Choice(choices: [StateEnum::Active, StateEnum::Inactive], message: 'Seleccione un estado válido.')]
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
        dump($this->getState()->value);
        $this->state = $this->getState()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(StateEnum::from($this->state));
    }
 }