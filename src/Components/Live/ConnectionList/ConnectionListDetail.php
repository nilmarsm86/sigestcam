<?php

namespace App\Components\Live\ConnectionList;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CameraRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
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

//    public function __construct(private readonly CameraRepository $cameraRepository)
//    {
//    }

//    public function mount(ConnectionType $connection, string $filter): void
//    {
//        $this->amount = 10;
//        $this->filter = $filter;
//        $this->page = 1;
//        $this->connection = $connection;
//        $this->reload();
//    }

//    private function reload()
//    {
//        $data = match ($this->connection) {
//            ConnectionType::Direct => $this->cameraRepository->findByDirectConnection($this->filter, $this->amount, $this->page),
////            ConnectionType::Simple => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
////            ConnectionType::SlaveSwitch => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
////            ConnectionType::SlaveModem => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
////            ConnectionType::Full => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
//        };
//
//        $this->reloadData($data);
//    }

//    /**
//     * Get change table event name
//     * @return string
//     */
//    public function getChangeTableEventName(): string
//    {
//        return static::CHANGE.'_'.$this->connection->name;
//    }

//    /**
//     * Get show detail event name
//     * @return string
//     */
//    private function getShowDetailEventName(): string
//    {
//        return static::DETAIL.'_'.$this->connection->name;
//    }

//    #[LiveAction]
//    public function status(): void
//    {
//        $this->reload();
//        $this->emit($this->getChangeTableEventName());
//    }

//    #[LiveAction]
//    public function disconnect(#[LiveArg] Camera $camera): void
//    {
//        $camera->disconnect();
//        $this->cameraRepository->save($camera, true);
//
//        $this->reload();
//        $this->emit($this->getChangeTableEventName());
//    }

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