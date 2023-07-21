<?php

namespace App\Entity;

use App\Entity\Enums\State as StateEnum;
use App\Entity\Interfaces\Harbor;
use App\Repository\CommutatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use App\Entity\Traits\Port as PortTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommutatorRepository::class)]
#[ORM\UniqueConstraint(name: 'ip', columns: ['ip'])]
#[DoctrineAssert\UniqueEntity('ip', message: 'El ip debe ser único.')]
class Commutator extends Equipment implements Harbor
{
    use PortTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El IP no debe estar vacío.')]
    #[Assert\NotNull(message: 'El IP no debe ser nulo.')]
    #[Assert\Ip(message:'Establezca un IP válido.')]
    private string $ip;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la cantidad de puertos.')]
    #[Assert\NotNull(message: 'La cantidad de puertos no debe ser nula.')]
    #[Assert\Positive]
    private int $portsAmount;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El IP gateway no debe estar vacío.')]
    #[Assert\NotNull(message: 'El IP gateway no debe ser nulo.')]
    #[Assert\Ip(message:'Establezca un IP gateway válido.')]
    private ?string $gateway = null;

    /**
     * @throws Exception
     */
    public function __construct(string $ip, int $portsAmount)
    {
        parent::__construct();
        $this->ports = new ArrayCollection();
        $this->ip = $ip;//validar que es un ip correcto
        $this->portsAmount = $portsAmount;
        $this->enumState = StateEnum::Active;
        $this->maximumPortsAmount = $portsAmount;
        $this->createPorts($this->portsAmount);
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    /*public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }*/

    public function configure(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPortsAmount(): ?int
    {
        return $this->portsAmount;
    }

    /*public function setPortsAmount(int $portsAmount): static
    {
        $this->portsAmount = $portsAmount;

        return $this;
    }*/

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
    public function maxPorts(): int
    {
        //deberia ser una validacion dinamica en dependencia de la cantidad de puertos pasaados
        return $this->maximumPortsAmount;
    }

}
