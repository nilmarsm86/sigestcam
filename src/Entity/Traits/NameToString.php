<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameToString
 {
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre no debe estar vacio.')]
    #[Assert\NotNull(message: 'El nombre no debe ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
 }