<?php

namespace App\Entity;

use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\PortType;
use App\Entity\Enums\State as StateEnum;
use App\Entity\Interfaces\HarborInterface;
use App\Entity\Traits\StateTrait as StateTrait;
use App\Repository\PortRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PortRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Port
{
    use StateTrait;

    const SPEED_TYPE = 'mb/s';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca el número del puerto.')]
//    #[Assert\NotNull(message: 'El número del puerto no puede ser nulo.')]
    #[Assert\PositiveOrZero]
    private ?int $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Establezca la velocidad del puerto.')]
//    #[Assert\NotNull(message: 'La velocidad del puerto no puede ser nula.')]
    #[Assert\PositiveOrZero]
    private float $speed;

    #[ORM\ManyToOne(inversedBy: 'ports')]
    #[Assert\Valid]
    private ?Commutator $commutator = null;

    #[ORM\ManyToOne(inversedBy: 'ports')]
    #[Assert\Valid]
    private ?Card $card = null;

    #[ORM\OneToOne(mappedBy: 'port', targetEntity: Equipment::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?Equipment $equipment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $connectionType = null;

    #[Assert\Choice(
        choices: [
            ConnectionType::Direct,
            ConnectionType::Full,
            ConnectionType::Simple,
            ConnectionType::SlaveModem,
            ConnectionType::SlaveSwitch],
        message: 'Seleccione un tipo de conexion válido.'
    )]
    private ?ConnectionType $enumConnectionType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $portType = null;

    #[Assert\Choice(
        choices: [PortType::Optic, PortType::Electric],
        message: 'Seleccione un tipo de puerto válido.'
    )]
    private ?PortType $enumPortType = null;

    public function __construct(?int $number = null, ?ConnectionType $connectionType = null)
    {
        $this->number = $number;
        $this->speed = 1;
        $this->enumState = StateEnum::Active;
        $this->enumPortType = PortType::Electric;
        if(!is_null($connectionType)){
            $this->enumConnectionType = $connectionType;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    /*public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }*/

    public function getSpeed(): float
    {
        return $this->speed;
    }

    /*public function setSpeed(float $speed): static
    {
        $this->speed = $speed;

        return $this;
    }*/

    public function getCommutator(): ?Commutator
    {
        return $this->commutator;
    }

    public function setCommutator(?Commutator $commutator): static
    {
        $this->commutator = $commutator;

        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): static
    {
        $this->card = $card;

        return $this;
    }

    public function isFromCommutator(): bool
    {
        return !is_null($this->getCommutator());
    }

    public function isFromCard(): bool
    {
        return !is_null($this->getCard());
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    /**
     * @throws Exception
     */
    public function setEquipment(?Equipment $equipment): static
    {
        if(!$equipment instanceof Modem){
            throw new Exception('Only modems can be connected directly to the card ports.');
        }

//        if (!is_null($equipment) && $this->isFromCard() && !$this->hasConnectedModem()) {
//
//        }

        $this->equipment = $equipment;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function connectCamera(Camera $camera): static
    {
        return $this->setEquipment($camera);
    }

    /**
     * @throws Exception
     */
    public function connectMsam(Msam $msam): static
    {
        return $this->setEquipment($msam);
    }

    /**
     * @throws Exception
     */
    public function connectModem(Modem $modem): static
    {
        return $this->setEquipment($modem);
    }

    /**
     * @throws Exception
     */
    public function connectServer(Server $server): static
    {
        return $this->setEquipment($server);
    }

    /**
     * @throws Exception
     */
    public function connectCommutator(Commutator $commutator): static
    {
        return $this->setEquipment($commutator);
    }

    public function hasConnectedCamera(): bool
    {
        return $this->getEquipment() instanceof Camera;
    }

    public function hasConnectedMsam(): bool
    {
        return $this->getEquipment() instanceof Msam;
    }

    public function hasConnectedModem(): bool
    {
        return $this->getEquipment() instanceof Modem;
    }

    public function hasConnectedServer(): bool
    {
        return $this->getEquipment() instanceof Server;
    }

    public function hasConnectedCommutator(): bool
    {
        return $this->getEquipment() instanceof Commutator;
    }

    public function configure(float $speed, ?PortType $type = null): static
    {
        $this->speed = $speed;
        if(!is_null($type)){
            $this->setPortType($type);
        }

        return $this;
    }

    /**
     * @param Port|null $port puerto al cual se pasara el equipo conectado a este
     * @return Port
     * @throws Exception
     */
    public function disabled(?Port $port): static
    {
        if (!is_null($this->equipment) && is_null($port)) {
            throw new Exception('This port must be disabled, but the equipment connected to it must be placed in another free port.');
        }

        //este puerto se desabilita
        $this->setState(StateEnum::Inactive);

        if (!is_null($this->getEquipment())) {
            //si el puerto tiene conectado algun equipo, ese equipo debe pasarce a otro puerto libre
            $port->setEquipment($this->getEquipment());
            //debo quitar el equipo de este puerto
            $this->setEquipment(null);
        }

        return $this;
    }

    /**
     * Si no tiene nada conectado el puerto esta libre
     * @return bool
     */
    public function isFree(): bool
    {
        return is_null($this->getEquipment());
    }

    /**
     * @param HarborInterface|null $harbor
     * @return $this
     */
    public function setHarbor(?HarborInterface $harbor): static
    {
        if($harbor instanceof Card){
            $this->setCard($harbor);
            return $this;
        }

        if($harbor instanceof Commutator){
            $this->setCommutator($harbor);
            return $this;
        }

        $this->setCard(null);
        $this->setCommutator(null);
        return $this;
    }

    /**
     * @return HarborInterface|null
     */
    public function getHarbor(): ?HarborInterface
    {
        if($this->isFromCard()){
            return $this->getCard();
        }

        if($this->isFromCommutator()){
            return $this->getCommutator();
        }

        return null;
    }

    public function getConnectionType(): ?ConnectionType
    {
        return $this->enumConnectionType;
    }

    /**
     * @throws Exception
     */
    public function setConnectionType(ConnectionType $connectionType): static
    {
        if(!$this->isFromCommutator()){
            throw new Exception('Solo los puertos de switch pueden tener el tipo de conexión.');
        }
        $this->connectionType = "0";
        $this->enumConnectionType = $connectionType;

        return $this;
    }

    public function getPortType(): ?PortType
    {
        return $this->enumPortType;
    }

    /**
     * @throws Exception
     */
    public function setPortType(PortType $portType): static
    {
        $this->portType = "";
        $this->enumPortType = $portType;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->state = $this->getState()->value;
        $this->portType = $this->getPortType()->value;
        if(!is_null($this->enumConnectionType)){
            if($this->enumConnectionType === ConnectionType::Null){
                $this->connectionType = null;
            }else{
                $this->connectionType = $this->getConnectionType()->value;
            }
        }
    }

    /**
     * @throws Exception
     */
    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setState(StateEnum::from($this->state));
        $this->setPortType(PortType::from($this->portType));
        if(!is_null($this->connectionType)){
            $this->setConnectionType(ConnectionType::from($this->connectionType));
        }
    }

    /**
     * Deactivate
     * @return $this
     */
    public function deactivate(): static
    {
        $this->getEquipment()?->deactivate();
        $this->state = null;
        $this->setState(StateEnum::Inactive);

        return $this;
    }

    public function getTypeValue():string
    {
        return PortType::getValueFrom($this->getPortType());
    }

    public function getTypeLabel():string
    {
        return PortType::getLabelFrom($this->getPortType());
    }

}
