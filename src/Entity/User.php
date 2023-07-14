<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'username', columns: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    private Collection $roles;

    #[ORM\Column]
    private ?bool $active = null;

    public function __construct(string $name, string $lastname, string $username, string $password)
    {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->username = $username;
        $this->password = $password;
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastame(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        /*$roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);*/

        $roles = [];
        foreach ($this->roles as $rol) {
            /** @var Role $rol */
            $roles[] = $rol->getName();
        }
        return array_unique($roles);
    }

    /*public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }*/

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function removeRole(Role $role, bool $secure = true): static
    {
        if($role->getName() === 'ROLE_USER'){
            throw new Exception('No puede ser eliminado el rol de usuario.');
        }

        if($secure){
            if(in_array('ROLE_ADMIN', $this->getRoles())){
                throw new Exception('No pueden ser eliminados los roles del administrador.');
            }
        }

        $this->roles->removeElement($role);

        return $this;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function register(UserPasswordHasherInterface $userPasswordHasher, Role $baseRol): static
    {
        $encodePassword = $userPasswordHasher->hashPassword($this,$this->password);
        $this->setPassword($encodePassword);
        $this->setActive(false);
        $this->addRole($baseRol);

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->getName().' '.$this->getLastame();
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    /**
     * Can change this user roles
     * @return bool
     */
    public function blockRoles(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * Can change this user roles
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return in_array('ROLE_SUPER_ADMIN', $this->getRoles());
    }
}
