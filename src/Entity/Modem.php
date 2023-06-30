<?php

namespace App\Entity;

use App\Entity\Traits\Connected;
use App\Repository\ModemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: ModemRepository::class)]
class Modem extends ConnectedElement
{
    use Connected;

    const MAXIMUM_CAMERA_NUMBER = 4;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $slaveModem = null;

    #[ORM\OneToMany(mappedBy: 'modem', targetEntity: Camera::class)]
    private Collection $cameras;

    public function __construct()
    {
        parent::__construct();
        $this->cameras = new ArrayCollection();
    }

    public function getSlaveModem(): ?self
    {
        return $this->slaveModem;
    }

    public function setSlaveModem(?self $slaveModem): static
    {
        $this->slaveModem = $slaveModem;

        return $this;
    }

    public function hasSlaveModem(): Bool
    {
        return !is_null($this->getSlaveModem());
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
        if($this->cameras->count() === self::MAXIMUM_CAMERA_NUMBER){
            throw new Exception('You have reached the maximum number of cameras allowed to connect.');
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

}
