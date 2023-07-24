<?php

namespace App\Entity;

use App\Entity\Enums\State as StateEnum;
use App\Entity\Traits\State as StateTrait;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['commutator' => 'Commutator', 'conn_element' => 'ConnectedElement'])]
#[ORM\HasLifecycleCallbacks]
class Equipment
{
    use StateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    protected Municipality $municipality;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La dirección física no debe estar vacía.')]
    #[Assert\NotNull(message: 'La dirección física no debe ser nula.')]
    protected string $physicalAddress;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El número de serie físico no debe estar vacío.')]
    #[Assert\NotNull(message: 'El número de serie físico no debe ser nulo.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de serie físico solo debe contener letras, números, guiones y punto.',
    )]
    protected string $physicalSerial;

    public function __construct()
    {
        $this->enumState = StateEnum::Active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMunicipality(): Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(Municipality $municipality): static
    {
        $this->municipality = $municipality;

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

    public function getPhysicalSerial(): string
    {
        return $this->physicalSerial;
    }

    public function setPhysicalSerial(string $physicalSerial): static
    {
        $this->physicalSerial = $physicalSerial;

        return $this;
    }

}
