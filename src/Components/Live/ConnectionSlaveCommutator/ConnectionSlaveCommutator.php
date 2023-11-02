<?php

namespace App\Components\Live\ConnectionSlaveCommutator;

use App\Components\Live\ConnectionCommutator\ConnectionCommutator;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Entity\Commutator;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_commutator/conecction_slave_commutator.html.twig')]
class ConnectionSlaveCommutator extends ConnectionCommutator
{
    const CHANGE = self::class.'_change';

    #[LiveProp]
    public ?Commutator $masterCommutator = null;

    #[LiveProp]
    public ?Port $masterPort = null;

    #[LiveProp]
    public bool $inactive = false;

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveSwitch')]
    public function onConnectionCommutatorPortListSelectedSlaveSwitch(#[LiveArg] ?Port $port): void
    {
        $this->masterCommutator = null;
        $this->masterPort = $port;
        $this->masterCommutator = $port?->getCommutator();
        $this->inactive = false;
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCommutatorTableDetailSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->masterCommutator = $entity;
        $this->masterPort = null;
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionCommutatorTableChangeSlaveSwitch(): void
    {
        $this->masterCommutator = null;
        $this->masterPort = null;
    }

    #[LiveAction]
    public function findInactive(): void
    {
        $this->inactive = true;
    }

    /**
     * Get change table event name
     * @return string
     */
    protected function getChangeFullComponentEventName(): string
    {
        return static::CHANGE.'_'.$this->connection->name;
    }

}