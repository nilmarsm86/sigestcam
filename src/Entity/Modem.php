<?php

namespace App\Entity;

use App\Entity\Enums\State;
use App\Entity\Enums\State as StateEnum;
use App\Entity\Traits\ConnectedTrait;
use App\Repository\ModemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModemRepository::class)]
class Modem extends Equipment
{
    use ConnectedTrait;

    const MAXIMUM_CONNECTED_NUMBER = 4;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'modems')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?self $masterModem = null;

    #[ORM\OneToMany(mappedBy: 'modem', targetEntity: Camera::class)]
    #[Assert\Count(
        max: 4,
        maxMessage: 'El modem no puede tener más de 4 cámaras conectadas.'
    )]
    private Collection $cameras;

    #[ORM\OneToMany(mappedBy: 'masterModem', targetEntity: self::class)]
    #[Assert\Count(
        max: 4,
        maxMessage: 'El modem no puede tener más de 4 modems esclavos conectados.'
    )]
    private Collection $modems;

    public function __construct($ip = null)
    {
        parent::__construct();
        $this->ip = $ip;
        $this->cameras = new ArrayCollection();
        $this->modems = new ArrayCollection();
        $this->enumState = State::Active;
    }

    public function getMasterModem(): ?self
    {
        return $this->masterModem;
    }

    public function setMasterModem(?self $masterModem): static
    {
        $this->masterModem = $masterModem;

        return $this;
    }

    public function hasMasterModem(): Bool
    {
        return !is_null($this->getMasterModem());
    }

    /**
     * @return Collection<int, Camera>
     */
    public function getCameras(): Collection
    {
        return $this->cameras;
    }

    /**
     * @throws Exception
     */
    public function addCamera(Camera $camera): static
    {
        if(!$this->canAddCameraOrModem()){
            throw new Exception('You have reached the maximum number of cameras/modems allowed to connect.');
        }

        if (!$this->cameras->contains($camera)) {
            $this->cameras->add($camera);
            $camera->setModem($this);
        }

        return $this;
    }

    public function removeCamera(Camera $camera): static
    {
        if ($this->cameras->removeElement($camera)) {
            // set the owning side to null (unless already changed)
            if ($camera->getModem() === $this) {
                $camera->setModem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Modem>
     */
    public function getModems(): Collection
    {
        return $this->modems;
    }

    /**
     * @throws Exception
     */
    public function addModem(Modem $modem): static
    {
        if(!$this->canAddCameraOrModem()){
            throw new Exception('You have reached the maximum number of cameras/modems allowed to connect.');
        }

        if (!$this->modems->contains($modem)) {
            $this->modems->add($modem);
            $modem->setMasterModem($this);
        }

        return $this;
    }

    public function removeModem(Modem $modem): static
    {
        if ($this->modems->removeElement($modem)) {
            // set the owning side to null (unless already changed)
            if ($modem->getMasterModem() === $this) {
                $modem->setMasterModem(null);
            }
        }

        return $this;
    }

    public function hasPort(): bool
    {
        return !is_null($this->port);
    }

    public function isInCardPort(): bool
    {
        return $this->port->isFromCard();
    }

    /**
     * Deactivate
     * @return $this
     */
    public function deactivate(): static
    {
        $this->masterModem?->deactivate();
        $this->deactivateCameras();

        return parent::deactivate();
    }

    /**
     * @return Modem
     */
    private function deactivateCameras(): static
    {
        foreach ($this->getCameras() as $camera) {
            /** @var Camera $camera */
            $camera->deactivate();
        }

        return $this;
    }

    private function deactivateModems(): static
    {
        foreach ($this->getModems() as $modem) {
            /** @var Modem $modem */
            $modem->deactivate();
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setStructuredCable(?StructuredCable $structuredCable): static
    {
        if(!$this->canHaveStructureCable()){
            throw new Exception('Solo pueden tener cable estructurado aquellos modems conectados a un Msam.');
        }

        $this->structuredCable = $structuredCable;

        return $this;
    }

    public function canHaveStructureCable(): bool
    {
        if(!$this->modemInCardPort()){
            return false;
        }

        return true;
    }

    public function modemInCardPort(): bool
    {
        if(!$this->hasPort()){
            return false;
        }

        if(!$this->isInCardPort()){
            return false;
        }

        return true;
    }

    public function canAddCameraOrModem(): bool
    {
        return $this->getConnectedNumber() < self::MAXIMUM_CONNECTED_NUMBER;
    }

    public function getConnectedNumber(): int
    {
        return $this->cameras->count() + $this->modems->count();
    }

    public function connect($container): static
    {
        if($container instanceof Modem){
            $this->setMasterModem($container);
        }

        if($container instanceof Port){
            $this->setPort($container);
        }

        return $this;
    }

}
