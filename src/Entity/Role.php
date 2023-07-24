<?php

namespace App\Entity;

use App\Entity\Traits\NameToString;
use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[DoctrineAssert\UniqueEntity(fields: ['name'], message: 'Ya existe un rol con este nombre.')]
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
     * @param string|null $role
     * @return string
     */
    public function capitalizeName(string $role = null): string
    {
        return match ($role ?: $this->getName()) {
            'ROLE_USER' => 'Rol usuario',
            'ROLE_TECHNICAL' => 'Rol técnico',
            'ROLE_OFFICER' => 'Rol oficial',
            'ROLE_BOSS' => 'Rol jefe',
            'ROLE_ADMIN' => 'Rol admin',
            'ROLE_SUPER_ADMIN' => 'Rol super admin',
        };
    }

    /**
     * Can change this role for all users
     * @return bool
     */
    public function blockChange(): bool
    {
        return $this->getName() === 'ROLE_USER';
    }

    public function isSuperAdmin(): bool
    {
        return $this->getName() === 'ROLE_SUPER_ADMIN';
    }

}
