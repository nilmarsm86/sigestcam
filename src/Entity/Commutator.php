<?php

namespace App\Entity;

use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\State;
use App\Entity\Interfaces\Harbor;
use App\Entity\Port as PortEntity;
use App\Repository\CommutatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\Entity\Traits\PortTrait as PortTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommutatorRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Commutator extends Equipment implements Harbor
{
    use PortTrait;

    #[ORM\OneToMany(mappedBy: 'commutator', targetEntity:PortEntity::class, cascade: ['persist'])]
    #[ORM\OrderBy(['number' => 'ASC'])]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 puerto para este equipo.',
    )]
    private Collection $ports;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la cantidad de puertos.')]
    #[Assert\NotNull(message: 'La cantidad de puertos no debe ser nula.')]
    #[Assert\Positive]
    private ?int $portsAmount;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El IP gateway no debe estar vacío.')]
    #[Assert\NotNull(message: 'El IP gateway no debe ser nulo.')]
    #[Assert\Ip(message:'Establezca un IP gateway válido.')]
    private ?string $gateway = null;

    /**
     * @param string|null $ip
     * @param int|null $portsAmount
     * @param ConnectionType|null $connectionType
     * @throws Exception
     */
    public function __construct(?string $ip = null, ?int $portsAmount = null, ?ConnectionType $connectionType = null)
    {
        parent::__construct();
        $this->ports = new ArrayCollection();
        $this->ip = $ip;//validar que es un ip correcto
        $this->portsAmount = $portsAmount;
        $this->enumState = State::Active;
        $this->maximumPortsAmount = $portsAmount;
        if(!is_null($portsAmount)){
            $this->createPorts($this->portsAmount, $connectionType);
        }
    }

    /*public function configure(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }*/

    public function getPortsAmount(): ?int
    {
        return $this->portsAmount;
    }

    public function setPortsAmount(int $portsAmount): static
    {
        $this->portsAmount = $portsAmount;

        return $this;
    }

    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    public function setGateway(string $gateway): static
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * No se pone en trait debido a la validación
     * @return int
     */
    #[Assert\Count(
        max: 32,
        maxMessage: 'Un switch tiene un máximo de {{ limit }} puertos.',
    )]
    public function getMaxPorts(): ?int
    {
        //deberia ser una validacion dinamica en dependencia de la cantidad de puertos pasaados
        return $this->maximumPortsAmount;
    }

}
