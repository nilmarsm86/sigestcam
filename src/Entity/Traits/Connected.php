<?php

namespace App\Entity\Traits;

use App\Entity\Camera;
use App\Entity\StructuredCable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

#[ORM\UniqueConstraint(name: 'ip', columns: ['ip'])]
#[Assert\UniqueEntity('ip', message: 'El ip debe ser único.')]
#[ORM\HasLifecycleCallbacks]
trait Connected
 {
    #[ORM\Column(length: 255)]
    private string $model;

    #[ORM\Column(length: 255)]
    private string $fisicalSerial;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\OneToOne(targetEntity: StructuredCable::class, cascade: ['persist', 'remove'])]
    private ?StructuredCable $structuredCable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $inventory = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    public function getFisicalSerial(): string
    {
        return $this->fisicalSerial;
    }

    public function setFisicalSerial(string $fisicalSerial): static
    {
        $this->fisicalSerial = $fisicalSerial;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

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

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function validateIp(): void
    {
        if($this instanceof Camera){
            if($this->getIp()){
                throw new \Exception('The camera needs to have an IP.');
            }
        }
    }

 }