<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/detail.html.twig')]
class ConnectionCommutatorDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    const DEACTIVATE = self::class.'_deactivate';

    #[LiveProp()]
    public ?array $commutator = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Commutator::class;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    protected function getDeactivateEventName(): string
    {
        return static::DEACTIVATE.'_'.$this->connection->name;
    }

    protected function onConnectionCommutatorTableDetail(#[LiveArg] Commutator $entity): void
    {
        if(isset($this->commutator['id'])){
            if($this->commutator['id'] !== $entity->getId()){
                $this->commutator = $this->details($entity);
                $this->active = $this->commutator['state'];
            }
        }else{
            $this->commutator = $this->details($entity);
            $this->active = $this->commutator['state'];
        }
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Direct')]
    public function onConnectionCommutatorTableDetailDirect(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Simple')]
    public function onConnectionCommutatorTableDetailSimple(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCommutatorTableDetailSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveModem')]
    public function onConnectionCommutatorTableDetailSlaveModem(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Full')]
    public function onConnectionCommutatorTableDetailFull(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    protected function details(Commutator $commutator): array
    {
        $switch = [];
        $switch['id'] = $commutator->getId();
        $switch['ip'] = $commutator->getIp();
        $switch['gateway'] = $commutator->getGateway();
        $switch['inventary'] = $commutator->getInventory();
        $switch['physical_address'] = $commutator->getPhysicalAddress();
        $switch['brand'] = $commutator->getBrand();
        $switch['model'] = $commutator->getModel();
        $switch['contic'] = $commutator->getContic();
        $switch['serial'] = $commutator->getPhysicalSerial();
        $switch['province'] = (string) $commutator->getMunicipality()->getProvince();
        $switch['municipality'] = (string) $commutator->getMunicipality();
        $switch['ports'] = $this->portsInfo($commutator);
        $switch['state'] = $commutator->isActive();
        $switch['multicast'] = $commutator->getMulticast();

        return $switch;
    }

    /**
     * Recollect ports info for commutator
     * @param Commutator $commutator
     * @return array
     */
    protected function portsInfo(Commutator $commutator): array
    {
        $ports = [];
        foreach($commutator->getPorts() as $port){
            $ports[$port->getId()] = $this->portData($port);
        }

        return $ports;
    }

    /**
     * Recollect port info
     * @param Port $port
     * @return array
     */
    protected function portData(Port $port): array
    {
        $data = [];
        $data['number'] = $port->getNumber();
        $data['state'] = $port->isActive();
        $data['equipment'] = $port?->getEquipment()?->getShortName();
        $data['speed'] = $port->getSpeed();
        $data['id'] = $port->getId();

        if (is_null($port->getConnectionType())) {
            $data['connection'] = 'bg-gradient-danger';
        } else {
            if ($port->getConnectionType() === $this->connection) {
                $data['connection'] = 'bg-gradient-success';
            } else {
                $data['connection'] = 'bg-gradient-primary';
            }
        }

        return $data;
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    protected function onConnectionCommutatorTableChange(): void
    {
        $this->commutator = null;
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Simple')]
    public function onConnectionCommutatorTableChangeSimple(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionCommutatorTableChangeSlaveSwitch(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveModem')]
    public function onConnectionCommutatorTableChangeSlaveModem(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Full')]
    public function onConnectionCommutatorTableChangeFull(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

}