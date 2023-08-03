<?php

namespace App\Entity;

use App\Entity\Traits\StateTrait as StateTrait;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\UniqueConstraint(name: 'ip', columns: ['ip'])]
#[DoctrineAssert\UniqueEntity('ip', message: 'El ip debe ser único.')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap([
    'msam' => 'Msam',
    'moden' => 'Modem',
    'camera' => 'Camera',
    'server' => 'Server',
    'commutator' => 'Commutator'
])]
#[ORM\HasLifecycleCallbacks]
class Equipment
{
    use StateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    //#[Assert\NotBlank(message: 'El tipo no debe estar vacío.')]
    //#[Assert\NotNull(message: 'El tipo no debe ser nulo.')]
    protected ?string $brand = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\When(
        expression: '$this instanceof Camera || $this instanceof Msam || $this instanceof Server || $this instanceof Commutator',
        constraints: [
            new Assert\NotBlank(message: 'Establezca el IP del equipo.'),
            new Assert\NotNull(message: 'El IP del equipo no puede ser nulo.')
        ],
    )]
    #[Assert\Ip(message:'Establezca un IP válido.')]
    protected ?string $ip = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La dirección física no debe estar vacía.')]
    #[Assert\NotNull(message: 'La dirección física no debe ser nula.')]
    protected string $physicalAddress;

    #[ORM\Column(length: 255, nullable: true)]
    /*#[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de serie físico solo debe contener letras, números, guiones y punto.',
    )]*/
    protected ?string $physicalSerial;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    protected ?Municipality $municipality = null;

    #[ORM\Column(length: 255, nullable: true)]
    /*#[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de inventario solo debe contener letras, números, guiones y punto.',
    )]*/
    protected ?string $model;

    #[ORM\Column(length: 255, nullable: true)]
    /*#[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de inventario solo debe contener letras, números, guiones y punto.',
    )]*/
    protected ?string $inventory = null;

    #[ORM\Column(length: 255, nullable: true)]
    /*#[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El contic solo debe contener letras, números, guiones y punto.',
    )]*/
    protected ?string $contic = null;

    #[ORM\OneToOne(inversedBy: 'equipment', targetEntity: Port::class)]
    #[Assert\Valid]
    protected ?Port $port = null;

    public function __construct()
    {
        //$this->enumState = StateEnum::Active;
        //parent::__construct();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

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

    public function getPhysicalAddress(): string
    {
        return $this->physicalAddress;
    }

    public function setPhysicalAddress(string $physicalAddress): static
    {
        $this->physicalAddress = $physicalAddress;

        return $this;
    }

    public function getPhysicalSerial(): ?string
    {
        return $this->physicalSerial;
    }

    public function setPhysicalSerial(?string $physicalSerial): static
    {
        $this->physicalSerial = $physicalSerial;

        return $this;
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): static
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = $model;

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

    public function getPort(): ?Port
    {
        return $this->port;
    }

    public function setPort(?port $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function __toString(): string
    {
        $namespace = explode('\\', static::class);
        //$className = $namespace[count($namespace) - 1];
        $data = $namespace[count($namespace) - 1];
        if(!is_null($this->getPhysicalSerial())){
            $data .= ': ('.$this->getPhysicalSerial().')';
        }

        if(!is_null($this->getIp())){
            $data .= '['.$this->getIp().']';
        }

        return $data;
    }

}
