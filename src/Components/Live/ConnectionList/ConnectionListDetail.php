<?php

namespace App\Components\Live\ConnectionList;

use App\Entity\Enums\ConnectionType;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_list/detail.html.twig')]
class ConnectionListDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public mixed $data = null;

    #[LiveProp]
    public ?int $entityId = null;

    /**
     * @param int $entity
     * @return void
     */
    public function onConnectionListTableDetail(int $entity): void
    {
        foreach($this->data as $key => $value){
            if($value['id'] === $entity){
                $this->entityId = $key;
            }
        }
    }

    #[LiveListener(ConnectionListTable::DETAIL.'_Direct')]
    public function onConnectionListTableDetailDirect(#[LiveArg] int $entity): void
    {
        $this->onConnectionListTableDetail($entity);
    }

    #[LiveListener(ConnectionListTable::DETAIL.'_Simple')]
    public function onConnectionListTableDetailSimple(#[LiveArg] int $entity): void
    {
        $this->onConnectionListTableDetail($entity);
    }

    #[LiveListener(ConnectionListTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionListTableDetailSlaveSwitch(#[LiveArg] int $entity): void
    {
        $this->onConnectionListTableDetail($entity);
    }

    #[LiveListener(ConnectionListTable::DETAIL.'_SlaveModem')]
    public function onConnectionListTableDetailSlaveModem(#[LiveArg] int $entity): void
    {
        $this->onConnectionListTableDetail($entity);
    }

    #[LiveListener(ConnectionListTable::DETAIL.'_Full')]
    public function onConnectionListTableDetailFull(#[LiveArg] int $entity): void
    {
        $this->onConnectionListTableDetail($entity);
    }

    public function onConnectionListTableChange(): void
    {
        $this->entityId = null;
    }

    #[LiveListener(ConnectionListTable::CHANGE.'_Direct')]
    public function onConnectionListTableChangeDirect(): void
    {
        $this->onConnectionListTableChange();
    }

    #[LiveListener(ConnectionListTable::CHANGE.'_Simple')]
    public function onConnectionListTableChangeSimple(): void
    {
        $this->onConnectionListTableChange();
    }

    #[LiveListener(ConnectionListTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionListTableChangeSlaveSwitch(): void
    {
        $this->onConnectionListTableChange();
    }

    #[LiveListener(ConnectionListTable::CHANGE.'_SlaveModem')]
    public function onConnectionListTableChangeSlaveModem(): void
    {
        $this->onConnectionListTableChange();
    }

}