<?php

namespace App\Entity;

use App\Repository\ConnectedElementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConnectedElementRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['msam' => 'Msam', 'moden' => 'Modem', 'camera' => 'Camera', 'server' => 'Server'])]
class ConnectedElement extends Equipment
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El tipo no debe estar vacío.')]
    #[Assert\NotNull(message: 'El tipo no debe ser nulo.')]
    protected ?string $brand = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\When(
        expression: '$this instanceof Camera || $this instanceof Msam || $this instanceof Server',
        constraints: [
            new Assert\NotBlank(message: 'Establezca el IP del equipo.'),
            new Assert\NotNull(message: 'El IP del equipo no puede ser nulo.')
        ],
    )]
    #[Assert\Ip(message:'Establezca un IP válido.')]
    protected ?string $ip = null;

    public function __construct()
    {
        parent::__construct();
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

    public function __toString(): string
    {
        $namespace = explode('\\', static::class);
        $className = $namespace[count($namespace) - 1];
        $data = $className.': ('.$this->getPhysicalSerial().')';
        if(!is_null($this->getIp())){
            $data .= '['.$this->getIp().']';
        }

        return $data;
    }

}
