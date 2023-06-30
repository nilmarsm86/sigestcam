<?php

namespace App\Entity\Traits;

use App\Entity\Card;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Port as PortEntity;
use Exception;

trait Port
{
    #[ORM\OneToMany(mappedBy: 'card', targetEntity:PortEntity::class)]
    #[ORM\OrderBy(['number' => 'ASC'])]
    private Collection $ports;
    private int $maximumPortsAmount;

    /**
     * @return Collection<int, PortEntity>
     */
    public function getPorts(): Collection
    {
        return $this->ports;
    }

    /**
     * @param PortEntity $port
     * @return Card
     * @throws Exception
     */
    private function addPort(PortEntity $port): static
    {
        if($this->ports->count() === $this->maximumPortsAmount){
            throw new Exception('You have reached the maximum number of ports allowed for this harbor.');
        }

        if (!$this->ports->contains($port)) {
            $this->ports->add($port);
            $port->setHarbor($this);
        }

        return $this;
    }

    /*public function removePort(Port $port): static
    {
        if ($this->ports->removeElement($port)) {
            // set the owning side to null (unless already changed)
            if ($port->getContainer() === $this) {
                $port->setHarbor(null);
            }
        }

        return $this;
    }*/

    /**
     * @param int $amount
     * @return Port
     * @throws Exception
     */
    public function createPorts(int $amount): static
    {
        if($this->ports->count() === $this->maximumPortsAmount){
            throw new Exception('You have reached the maximum number of ports allowed for this harbor.');
        }

        for ($i = 0; $i < $amount; $i++) {
            $this->addPort(new PortEntity($i + 1));
        }

        return $this;
    }
}