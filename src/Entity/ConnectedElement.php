<?php

namespace App\Entity;

use App\Repository\ConnectedElementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectedElementRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['msam' => 'Msam', 'moden' => 'Modem', 'camera' => 'Camera'])]
class ConnectedElement extends Equipment
{
    #[ORM\Column(length: 255)]
    protected ?string $brand = null;

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

}
