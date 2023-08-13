<?php
namespace App\Components\Live\ConnectionCamera;

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

#[AsLiveComponent(template: 'components/live/connection_camera/connection_camera.html.twig')]
class ConnectionCamera
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Port $port = null;

    //#[LiveProp]
    //public ?Modem $modem = null;

    #[LiveProp]
    public ?Commutator $commutator = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Direct')]
    public function onConnectionCommutatorPortListSelectedDirect(#[LiveArg] ?Port $port): void
    {
        $this->commutator = null;
        $this->port = $port;
        $this->commutator = $port?->getCommutator();
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Direct')]
    public function onConnectionCommutatorTableDetailDirect(#[LiveArg] Commutator $entity): void
    {
        $this->commutator = $entity;
        $this->port = null;
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
    }

}