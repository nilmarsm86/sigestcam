<?php

namespace App\Entity\Traits;

use App\Entity\Enums\State as StateEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait StateTrait
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
        $this->state = $this->getState()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(StateEnum::from($this->state));
    }

    /**
     * Is active or not
     * @return bool
     */
    public function isActive():bool
    {
        return $this->enumState === StateEnum::Active;
    }

    /**
     * Activate
     * @return $this
     */
    public function activate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Active);
        return $this;
    }

    /**
     * Deactivate
     * @return $this
     */
    public function deactivate(): static
    {
        $this->state = null;
        $this->setState(StateEnum::Inactive);

        return $this;
    }
 }