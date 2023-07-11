<?php

namespace App\Entity;

use App\Entity\Traits\NameToString;
use App\Repository\MunicipalityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[Assert\UniqueEntity('name', message: 'El municipio debe ser único.')]
class Municipality
{
    use NameToString;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'municipalities')]
    #[ORM\JoinColumn(nullable: false)]
    private Province $province;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvince(): Province
    {
        return $this->province;
    }

    public function setProvince(Province $province): static
    {
        $this->province = $province;

        return $this;
    }


}
