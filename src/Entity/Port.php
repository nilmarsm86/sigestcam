<?php

namespace App\Entity;

use App\Entity\Enums\State as StateEnum;
use App\Entity\Interfaces\Harbor;
use App\Entity\Traits\State as StateTrait;
use App\Repository\PortRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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
    private int $number;

    #[ORM\Column]
    private float $speed = 1;

    #[ORM\ManyToOne(inversedBy: 'ports')]
    private ?Commutator $commutator = null;

    #[ORM\ManyToOne(inversedBy: 'ports')]
    private ?Card $card = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ConnectedElement $connectedElement = null;

    public function __construct(int $number)
    {
        $this->number = $number;
        $this->speed = 1;
        $this->enumState = StateEnum::Active;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    /*public function setNumber(int $number): static
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

    public function getConnectedElement(): ?ConnectedElement
    {
        return $this->connectedElement;
    }

    /**
     * @throws Exception
     */
    public function setConnectedElement(?ConnectedElement $connectedElement): static
    {
        if ($this->isFromCommutator() && !$this->hasConnectedCamera()) {
            throw new Exception('Only cameras can be connected directly to the switch ports.');
        }

        $this->connectedElement = $connectedElement;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function connectCamera(Camera $camera): static
    {
        return $this->setConnectedElement($camera);
    }

    /**
     * @throws Exception
     */
    public function connectMsam(Msam $msam): static
    {
        return $this->setConnectedElement($msam);
    }

    /**
     * @throws Exception
     */
    public function connectModem(Modem $modem): static
    {
        return $this->setConnectedElement($modem);
    }

    public function hasConnectedCamera(): bool
    {
        return $this->getConnectedElement() instanceof Camera;
    }

    public function hasConnectedMsam(): bool
    {
        return $this->getConnectedElement() instanceof Msam;
    }

    public function hasConnectedModem(): bool
    {
        return $this->getConnectedElement() instanceof Modem;
    }

    public function configure(float $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * @param Port|null $port puerto al cual se pasara el equipo conectado a este
     * @return Port
     * @throws Exception
     */
    public function disabled(?Port $port): static
    {
        if (!is_null($this->connectedElement) && is_null($port)) {
            throw new Exception('This port must be disabled, but the equipment connected to it must be placed in another free port.');
        }

        //este puerto se desabilita
        $this->setState(StateEnum::Inactive);

        if (!is_null($this->getConnectedElement())) {
            //si el puerto tiene conectado algun equipo, ese equipo debe pasarce a otro puerto libre
            $port->setConnectedElement($this->getConnectedElement());
            //debo quitar el equipo de este puerto
            $this->setConnectedElement(null);
        }

        return $this;
    }

    /**
     * Si no tiene nada conectado el puerto esta libre
     * @return bool
     */
    public function isFree(): bool
    {
        return is_null($this->getConnectedElement());
    }

    /**
     * @param Harbor|null $harbor
     * @return $this
     */
    public function setHarbor(?Harbor $harbor): static
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
     * @return Harbor|null
     */
    public function getHarbor(): ?Harbor
    {
        if($this->isFromCard()){
            return $this->getCard();
        }

        if($this->isFromCommutator()){
            return $this->getCommutator();
        }

        return null;
    }

}
