<?php

namespace App\Entity;

use App\Entity\Traits\NameToString;
use App\Repository\OrganRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: OrganRepository::class)]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity('name', message: 'El órgano debe ser único.')]
class Organ
{
    use NameToString;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

}
