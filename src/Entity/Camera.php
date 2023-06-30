<?php

namespace App\Entity;

use App\Entity\Traits\Connected;
use App\Repository\CameraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CameraRepository::class)]
class Camera extends ConnectedElement
{
    use Connected;

    #[ORM\Column(length: 255)]
    private ?string $user = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'cameras')]
    private ?Modem $modem = null;

    public function __construct()
    {
        parent::__construct();
    }

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

}
