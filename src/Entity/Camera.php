<?php

namespace App\Entity;

use App\Entity\Traits\Connected;
use App\Repository\CameraRepository;
use App\Validator\Username;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CameraRepository::class)]
#[Assert\Cascade]
#[ORM\HasLifecycleCallbacks]
class Camera extends ConnectedElement
{
    use Connected;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La camara debe de tener un usuario.')]
    #[Assert\NotNull(message: 'El usuario de la camara no puede ser nulo.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    private ?string $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La camara debe de tener una contraseña.')]
    #[Assert\NotNull(message: 'la contraseña de la camara no puede ser nula.')]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'cameras')]
    #[Assert\Valid]
    private ?Modem $modem = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_\-\.]+$/',
        message: 'El número de serie electrónico solo debe contener letras, números, guiones y punto.',
    )]
    private ?string $electronicSerial = null;

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getModem(): ?Modem
    {
        return $this->modem;
    }

    public function setModem(?Modem $modem): static
    {
        $this->modem = $modem;

        return $this;
    }

    public function getElectronicSerial(): ?string
    {
        return $this->electronicSerial;
    }

    public function setElectronicSerial(?string $electronicSerial): static
    {
        $this->electronicSerial = $electronicSerial;

        return $this;
    }

}
