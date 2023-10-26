<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Msam;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/conecction_msam.html.twig')]
class ConnectionMsam
{
    use DefaultActionTrait;

//    #[LiveProp]
//    public ?Msam $msam = null;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Commutator $commutator = null;

    #[LiveProp(writable: true)]
    public ?ConnectionType $connection = null;

    protected function onConnectionCommutatorPortListSelected(?Port $port): void
    {
        $this->commutator = null;
        $this->port = $port;
        $this->commutator = $port?->getCommutator();
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Full')]
    public function onConnectionCommutatorPortListSelectedFull(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    public function onConnectionCommutatorTableDetail(Commutator $entity): void
    {
        $this->commutator = $entity;
        $this->port = null;
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Full')]
    public function onConnectionCommutatorTableDetailSimple(#[LiveArg] Commutator $entity): void
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

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Full')]
    public function onConnectionCommutatorTableChangeSimple(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

}