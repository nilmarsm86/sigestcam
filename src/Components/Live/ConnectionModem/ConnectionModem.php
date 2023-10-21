<?php
namespace App\Components\Live\ConnectionModem;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_modem/connection_modem.html.twig')]
class ConnectionModem
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Commutator $commutator = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function onConnectionCommutatorPortListSelected(?Port $port): void
    {
        $this->commutator = null;
        $this->port = $port;
        $this->commutator = $port?->getCommutator();
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Simple')]
    public function onConnectionCommutatorPortListSelectedSimple(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveModem')]
    public function onConnectionCommutatorPortListSelectedSlaveModem(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    public function onConnectionCommutatorTableDetail(Commutator $entity): void
    {
        $this->commutator = $entity;
        $this->port = null;
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Simple')]
    public function onConnectionCommutatorTableDetailSimple(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveModem')]
    public function onConnectionCommutatorTableDetailSlaveModem(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    public function onConnectionCommutatorTableChange(): void
    {
        $this->commutator = null;
        $this->port = null;
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Simle')]
    public function onConnectionCommutatorTableChangeSimple(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveModem')]
    public function onConnectionCommutatorTableChangeSlaveModem(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

}