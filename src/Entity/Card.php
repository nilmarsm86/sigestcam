<?php

namespace App\Entity;

use App\Entity\Interfaces\HarborInterface;
use App\Entity\Port as PortEntity;
use App\Entity\Traits\NameToStringTrait;
use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\Entity\Traits\PortTrait as PortTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card implements HarborInterface
{
    use PortTrait;
    use NameToStringTrait;

    const MAXIMUM_PORTS_NUMBER = 16;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity:PortEntity::class, cascade: ['persist'])]
    #[ORM\OrderBy(['number' => 'ASC'])]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 puerto para este equipo.',
    )]
    private Collection $ports;

    /*#[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre no debe estar vacío.')]
    #[Assert\NotNull(message: 'El nombre no debe ser nulo.')]
    private string $name;*/

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Msam $msam = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El slot no debe estar vacío.')]
//    #[Assert\NotNull(message: 'El slot no debe ser nulo.')]
    #[Assert\Positive]
    private ?int $slot = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->ports = new ArrayCollection();
        $this->maximumPortsAmount = self::MAXIMUM_PORTS_NUMBER;
        $this->createPorts(self::MAXIMUM_PORTS_NUMBER, null);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /*public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }*/

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

    public function setSlot(?int $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

    /**
     * No se pone en trait debido a la validación
     * @return int
     */
    #[Assert\LessThanOrEqual(16)]
    public function maxPorts(): int
    {
        return $this->maximumPortsAmount;
    }

    public function deactivate(): static
    {
        $this->deactivatePorts();

        return $this;
    }

}
