<?php
namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionModem\ConnectionModemDetail;
use App\Components\Live\ConnectionModem\ConnectionModemTable;
use App\Components\Live\ConnectionSlaveCommutator\ConnectionSlaveCommutatorPortList;
use App\Components\Live\ConnectionSlaveCommutator\ConnectionSlaveCommutatorTable;
use App\Components\Live\ConnectionSlaveModem\ConnectionSlaveModemDetail;
use App\Components\Live\ConnectionSlaveModem\ConnectionSlaveModemTable;
use App\Entity\Card;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Msam;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/connection_camera.html.twig')]
class ConnectionCamera
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Modem $modem = null;

    #[LiveProp]
    public ?Commutator $commutator = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?Card $card = null;

    #[LiveProp]
    public ?Msam $msam = null;

    public function onConnectionCommutatorPortListSelected(?Port $port): void
    {
        $this->commutator = null;
        $this->port = $port;
        $this->commutator = $port?->getCommutator();
        $this->modem = null;
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Direct')]
    public function onConnectionCommutatorPortListSelectedDirect(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Simple')]
    public function onConnectionCommutatorPortListSelectedSimple(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionSlaveCommutatorPortList::SELECTED.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorPortListSelectedSlaveSwitch(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveModem')]
    public function onConnectionCommutatorPortListSelectedSlaveModem(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    protected function onConnectionCommutatorTableDetail(Commutator $entity): void
    {
        $this->commutator = $entity;
        $this->port = null;
        $this->modem = null;
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

    #[LiveListener(ConnectionSlaveCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorTableDetailSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionSlaveCommutatorTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorTableChangeSlaveSwitch(): void
    {
        $this->commutator = null;
        $this->port = null;
        $this->modem = null;
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->commutator = null;
        $this->port = null;
        $this->modem = null;
    }

    #[LiveListener(ConnectionModemTable::DETAIL.'_Simple')]
    public function onConnectionModemTableDetailSimple(#[LiveArg] Modem $entity): void
    {
        $this->modem = $entity;
        $this->port = $entity->getPort() ?: $this->port;
        $this->commutator = $this->port->getCommutator();
    }

    #[LiveListener(ConnectionModemTable::DETAIL.'_Full')]
    public function onConnectionModemTableDetailFull(#[LiveArg] Modem $entity): void
    {
        $this->modem = $entity;
        $this->port = $entity->getPort() ?: $this->port;
        //$this->commutator = $this->port->getCommutator();
        $this->card = $entity->getPort()->getCard();
        $this->msam = $entity->getPort()->getCard()->getMsam();
    }

    #[LiveListener(ConnectionSlaveModemTable::DETAIL.'_SlaveModem')]
    public function onConnectionSlaveModemTableDetailSlaveModem(#[LiveArg] Modem $entity): void
    {
        $this->modem = $entity;
        $this->port = $entity->getMasterModem()?->getPort() ?: $this->port;
        $this->commutator = $this->port->getCommutator();
    }

    private function onConnectionModemChange(): void
    {
        $this->commutator = null;
        $this->port = null;
        $this->modem = null;
    }

    #[LiveListener(ConnectionModemTable::CHANGE.'_Simple')]
    public function onConnectionModemTableChangeSimple(): void
    {
        $this->onConnectionModemChange();
    }

    #[LiveListener(ConnectionSlaveModemTable::CHANGE.'_SlaveModem')]
    public function onConnectionSlaveModemTableChangeSlaveModem(): void
    {
        $this->onConnectionModemChange();
    }

    private function onConnectionModemDetailDeactivate(): void
    {
        $this->modem = null;
    }

    #[LiveListener(ConnectionModemDetail::DEACTIVATE.'_Simple')]
    public function onConnectionModemDetailDeactivateSimple(): void
    {
        $this->onConnectionModemDetailDeactivate();
    }

    #[LiveListener(ConnectionSlaveModemDetail::DEACTIVATE.'_SlaveModem')]
    public function onConnectionSlaveModemDetailDeactivateSlaveModem(): void
    {
        $this->onConnectionModemDetailDeactivate();
    }

    #[LiveAction]
    public function removeModem(): void
    {
        $this->modem = new Modem();
    }



}