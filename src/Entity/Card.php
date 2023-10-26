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
#[ORM\HasLifecycleCallbacks]
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
//    #[Assert\Count(
//        min: 1,
//        minMessage: 'Debe establecer al menos 1 puerto para este equipo.',
//    )]
    private Collection $ports;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Msam $msam = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El slot no debe estar vacío.')]
//    #[Assert\NotNull(message: 'El slot no debe ser nulo.')]
    #[Assert\Positive]
    private ?int $slot = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la cantidad de puertos.')]
//    #[Assert\NotNull(message: 'La cantidad de puertos no debe ser nula.')]
    #[Assert\Positive]
    private ?int $portsAmount;


    /**
     * @throws Exception
     */
    public function __construct(?int $portsAmount = null)
    {
        $this->ports = new ArrayCollection();
        $this->portsAmount = $portsAmount;
        $this->maximumPortsAmount = self::MAXIMUM_PORTS_NUMBER;
        if(!is_null($portsAmount)) {
            $this->createPorts(self::MAXIMUM_PORTS_NUMBER, null);
        }
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
//    #[Assert\LessThanOrEqual(16)]
    public function maxPorts(): int
    {
        return $this->maximumPortsAmount;
    }

    public function deactivate(): static
    {
        $this->deactivatePorts();

        return $this;
    }

    public function getPortsAmount(): ?int
    {
        return $this->portsAmount;
    }

    public function setPortsAmount(int $portsAmount): static
    {
        $this->portsAmount = $portsAmount;
        $this->maximumPortsAmount = $portsAmount;
        return $this;
    }

    #[ORM\PrePersist]
    public function onCreatePort(): void
    {
        if(!is_null($this->getPortsAmount()) && $this->ports->count() === 0){
            $this->createPorts($this->portsAmount);
        }
    }

}
