<?php

namespace App\Components\Live\ConnectionSlaveCommutator;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
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

#[AsLiveComponent(template: 'components/live/connection_slave_commutator/port_list.html.twig')]
class ConnectionSlaveCommutatorPortList extends ConnectionCommutatorPortList
{
//    use DefaultActionTrait;
//    use ComponentActiveInactive;
    use ComponentToolsTrait;

    const DEACTIVATE = self::class.'_deactivate';
    const ACTIVATE = self::class.'_activate';
    const SELECTED = self::class.'_selected';

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

    /**
     * Get deactivate event name
     * @return string
     */
    protected function getDeactivateEventName(): string
    {
        return static::DEACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    protected function getActivateEventName(): string
    {
        return static::ACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    protected function getSelectedEventName(): string
    {
        return static::SELECTED.'_'.$this->connection->name;
    }

}