<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

#[ORM\UniqueConstraint(name: 'ip', columns: ['ip'])]
#[Assert\UniqueEntity('ip', message: 'El ip debe ser único.')]
trait Connected
 {
    #[ORM\Column(length: 255)]
    private string $model;

    #[ORM\Column(length: 255)]
    private string $serial;

    #[ORM\Column(length: 255)]
    private string $ip;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $structuredCable = null;

    #[ORM\Column(length: 255)]
    private string $inventory;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getSerial(): string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): static
    {
        $this->serial = $serial;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getStructuredCable(): ?string
    {
        return $this->structuredCable;
    }

    public function setStructuredCable(string $structuredCable): static
    {
        $this->structuredCable = $structuredCable;

        return $this;
    }

    public function getInventory(): string
    {
        return $this->inventory;
    }

    public function setInventory(string $inventory): static
    {
        $this->inventory = $inventory;

        return $this;
    }

 }