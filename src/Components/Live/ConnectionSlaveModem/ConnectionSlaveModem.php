<?php
namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionModem\ConnectionModem;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
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

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveModem')]
    public function onConnectionCommutatorPortListSelectedSlaveModem(#[LiveArg] ?Port $port): void
    {
//        $this->commutator = null;
//        $this->port = $port;
//        $this->commutator = $port?->getCommutator();
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

}