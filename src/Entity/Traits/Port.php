<?php

namespace App\Entity\Traits;

use App\Entity\Card;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Port as PortEntity;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

trait Port
{
    #[ORM\OneToMany(mappedBy: 'card', targetEntity:PortEntity::class)]
    #[ORM\OrderBy(['number' => 'ASC'])]
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 puerto para este equipo.',
    )]
    private Collection $ports;

    private ?int $maximumPortsAmount;

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
            throw new Exception('Ha alcanzado el número máximo de puertos permitidos para este equipo.');
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
            throw new Exception('Ha alcanzado el número máximo de puertos permitidos para este puerto.');
        }

        for ($i = 0; $i < $amount; $i++) {
            $this->addPort(new PortEntity($i + 1));
        }

        return $this;
    }
}