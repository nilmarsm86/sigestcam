<?php

namespace App\Entity\Traits;

use App\Entity\StructuredCable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\UniqueConstraint(name: 'ip', columns: ['ip'])]
#[DoctrineAssert\UniqueEntity('ip', message: 'El ip debe ser único.')]
trait Connected
 {
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La camara debe de tener un usuario.')]
    #[Assert\NotNull(message: 'El usuario de la camara no puede ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    private string $model;

    #[ORM\OneToOne(targetEntity: StructuredCable::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?StructuredCable $structuredCable = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de inventario solo debe contener letras, números, guiones y punto.',
    )]
    private ?string $inventory = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El contic solo debe contener letras, números, guiones y punto.',
    )]
    private ?string $contic = null;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getStructuredCable(): ?StructuredCable
    {
        return $this->structuredCable;
    }

    public function setStructuredCable(StructuredCable $structuredCable): static
    {
        $this->structuredCable = $structuredCable;

        return $this;
    }

    public function getInventory(): ?string
    {
        return $this->inventory;
    }

    public function setInventory(?string $inventory): static
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getContic(): ?string
    {
        return $this->contic;
    }

    public function setContic(?string $contic): static
    {
        $this->contic = $contic;

        return $this;
    }

 }