<?php

namespace App\Entity;

use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\State;
use App\Entity\Traits\ConnectedTrait;
use App\Repository\CameraRepository;
use App\Validator\Username;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CameraRepository::class)]
#[Assert\Cascade]
#[ORM\HasLifecycleCallbacks]
class Camera extends Equipment
{
    use ConnectedTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La camara debe de tener un usuario.')]
    #[Assert\NoSuspiciousCharacters]
    #[Username]
    private ?string $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La cámara debe de tener una contraseña.')]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'cameras')]
    #[Assert\Valid]
    private ?Modem $modem = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NoSuspiciousCharacters]
    private ?string $electronicSerial = null;

    /**
     * @param string|null $ip
     */
    public function __construct(?string $ip = null)
    {
        parent::__construct();
        $this->ip = $ip;//validar que es un ip correcto
        $this->enumState = State::Active;
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

    public function getElectronicSerial(): ?string
    {
        return $this->electronicSerial;
    }

    public function setElectronicSerial(?string $electronicSerial): static
    {
        $this->electronicSerial = $electronicSerial;

        return $this;
    }

    public function hasModem(): bool
    {
        return !is_null($this->modem);
    }

    /**
     * @throws Exception
     */
    public function setStructuredCable(?StructuredCable $structuredCable): static
    {
        if(!$this->canHaveStructureCable()){
            throw new Exception('Solo pueden tener cable estructurado aquellas cámaras conectadas a un modem conectado a un Msam.');
        }

        $this->structuredCable = $structuredCable;

        return $this;
    }

    public function modemHasPort(): bool
    {
        if(!$this->hasModem()){
            return false;
        }

        if(!$this->modem->hasPort()){
            return false;
        }

        return true;
    }

    public function modemInCardPort(): bool
    {
        if(!$this->modemHasPort()){
            return false;
        }

        if(!$this->modem->isInCardPort()){
            return false;
        }

        return true;
    }

    public function canHaveStructureCable(): bool
    {
        if(!$this->modemInCardPort()){
            return false;
        }

        return true;
    }

    public function disconnect(): static
    {
        if(!is_null($this->port)){
            $this->port->setEquipment(null);
            if(!is_null($this->port->getConnectionType())){
                $this->port->setConnectionType(ConnectionType::Null);
            }
            $this->port = null;
        }

        if(!is_null($this->modem)){
            $this->modem->removeCamera($this);
        }

        $this->deactivate();

        return $this;
    }

    public function connect($container): static
    {
        if($container instanceof Modem){
            $this->setModem($container);
        }

        if($container instanceof Port){
            $this->setPort($container);
        }

        return $this;
    }

    public function isDisconnected()
    {
        return is_null($this->port) && is_null($this->modem);
    }

    public function notModemNotPort(): bool
    {
        return (is_null($this->getPort()) && is_null($this->getModem()));
    }

}
