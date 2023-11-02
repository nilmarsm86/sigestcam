<?php

namespace App\Components\Live\ConnectionSlaveCommutator;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorDetail;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Commutator;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_commutator/detail.html.twig')]
class ConnectionSlaveCommutatorDetail extends ConnectionCommutatorDetail
{
    use ComponentToolsTrait;

    const DEACTIVATE = self::class.'_deactivate';

    #[LiveProp(updateFromParent: true)]
    public ?Port $masterPort = null;

    #[LiveListener(ConnectionSlaveCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorTableDetailSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionSlaveCommutatorTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorTableChangeSlaveSwitch(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

}