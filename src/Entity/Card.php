<?php

namespace App\Entity;

use App\Entity\Interfaces\Harbor;
use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\Entity\Traits\Port as PortTrait;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card implements Harbor
{
    use PortTrait;

    const MAXIMUM_PORTS_NUMBER = 16;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Msam $msam = null;

    #[ORM\Column]
    private int $slot;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->ports = new ArrayCollection();
        $this->maximumPortsAmount = self::MAXIMUM_PORTS_NUMBER;
        $this->createPorts(self::MAXIMUM_PORTS_NUMBER);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMsam(): ?Msam
    {
        return $this->msam;
    }

    public function setMsam(?Msam $msam): static
    {
        $this->msam = $msam;

        return $this;
    }

    public function getSlot(): ?int
    {
        return $this->slot;
    }

    public function setSlot(int $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

}
