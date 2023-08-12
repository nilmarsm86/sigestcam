<?php

namespace App\Entity\Traits;

use App\Entity\Card;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Port as PortEntity;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

trait PortTrait
{
    private ?int $maximumPortsAmount = null;

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

    /*public function removePort(PortTrait $port): static
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
     * @param ConnectionType|null $connectionType
     * @return PortTrait
     * @throws Exception
     */
    public function createPorts(int $amount, ?ConnectionType $connectionType = null): static
    {
        if($this->ports->count() === $this->maximumPortsAmount){
            throw new Exception('Ha alcanzado el número máximo de puertos permitidos para este puerto.');
        }

        for ($i = 0; $i < $amount; $i++) {
            $this->addPort(new PortEntity($i + 1, $connectionType));
        }

        return $this;
    }

    /**
     * @return void
     */
    private function deactivatePorts(): void
    {
        foreach ($this->getPorts() as $port) {
            /** @var Port $port */
            $port->deactivate();
        }
    }

    /*public function hasPort(int $portId): bool
    {
        $this->ports->containsKey();
        return false;
    }*/
}