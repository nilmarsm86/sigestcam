<?php

namespace App\Entity;

use App\Repository\StructuredCableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructuredCableRepository::class)]
class StructuredCable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $physicalAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $point = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $feederCable = null;

    #[ORM\Column(length: 255)]
    private ?string $pair = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhysicalAddress(): ?string
    {
        return $this->physicalAddress;
    }

    public function setPhysicalAddress(string $physicalAddress): static
    {
        $this->physicalAddress = $physicalAddress;

        return $this;
    }

    public function getPoint(): ?string
    {
        return $this->point;
    }

    public function setPoint(string $point): static
    {
        $this->point = $point;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getFeederCable(): ?string
    {
        return $this->feederCable;
    }

    public function setFeederCable(string $feederCable): static
    {
        $this->feederCable = $feederCable;

        return $this;
    }

    public function getPair(): ?string
    {
        return $this->pair;
    }

    public function setPair(string $pair): static
    {
        $this->pair = $pair;

        return $this;
    }
}
