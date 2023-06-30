<?php

namespace App\Entity;

use App\Entity\Enums\State as StateEnum;
use App\Entity\Traits\State as StateTrait;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['commutator' => 'Commutator', 'conn_element' => 'ConnectedElement'])]
class Equipment
{
    use StateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    protected Municipality $municipality;

    #[ORM\Column(length: 255)]
    protected string $physicalAddress;

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

}