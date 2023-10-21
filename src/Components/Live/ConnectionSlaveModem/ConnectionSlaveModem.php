<?php
namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionModem\ConnectionModem;
use App\Components\Live\ConnectionModem\ConnectionModemDetail;
use App\Components\Live\ConnectionModem\ConnectionModemTable;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_modem/connection_slave_modem.html.twig')]
class ConnectionSlaveModem extends ConnectionModem
{
//    use DefaultActionTrait;

//    #[LiveProp]
//    public ?Port $port = null;

//    #[LiveProp]
//    public ?Commutator $commutator = null;

//    #[LiveProp]
//    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?Modem $masterModem = null;

    #[LiveProp]
    public bool $inactives = false;

    #[LiveListener(ConnectionModemTable::DETAIL.'_SlaveModem')]
    public function onConnectionModemTableDetailSlaveModem(#[LiveArg] Modem $entity): void
    {
        $this->masterModem = $entity;
    }

    #[LiveListener(ConnectionModemTable::CHANGE.'_SlaveModem')]
    public function onConnectionModemTableChangeSlaveModem(): void
    {
        $this->masterModem = null;
    }

    #[LiveListener(ConnectionModemDetail::DEACTIVATE.'_SlaveModem')]
    public function onConnectionModemDetailDeactivateSlaveModem(): void
    {
        $this->masterModem = null;
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveModem')]
    public function onConnectionCommutatorPortListSelectedSlaveModem(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
        $this->masterModem = null;
    }

//    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Simple')]
//    public function onConnectionCommutatorTableDetailSimple(#[LiveArg] Commutator $entity): void
//    {
//        $this->commutator = $entity;
//        $this->port = null;
//    }

//    /**
//     * Update table from filter, amount or page just in direct connections
//     * @return void
//     */
//    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Simle')]
//    public function onConnectionCommutatorTableChangeSimple(): void
//    {
//        $this->commutator = null;
//        $this->port = null;
//    }

    #[LiveAction]
    public function inactiveModems(): void
    {
        $this->inactives = true;
    }

    #[LiveAction]
    public function activeModems(): void
    {
        $this->inactives = false;
    }

}