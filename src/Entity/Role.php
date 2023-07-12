<?php

namespace App\Entity;

use App\Entity\Traits\NameToString;
use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'Ya existe un rol con este nombre.')]
class Role
{
    use NameToString;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Return translate rol name
     * @return string
     */
    public function capitalizeName(): string
    {
        return match ($this->getName()) {
            'ROLE_USER' => 'Rol usuario',
            'ROLE_TECHNICAL' => 'Rol técnico',
            'ROLE_OFFICER' => 'Rol oficial',
            'ROLE_BOSS' => 'Rol jefe',
            'ROLE_ADMIN' => 'Rol admin',
        };
    }

}
