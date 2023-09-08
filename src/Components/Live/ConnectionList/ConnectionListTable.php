<?php

namespace App\Components\Live\ConnectionList;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentTable;
use App\DTO\Paginator;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use App\Repository\CameraRepository;
use App\Repository\CommutatorRepository;
use App\Repository\PortRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_list/table.html.twig')]
class ConnectionListTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(private readonly CameraRepository $cameraRepository)
    {
    }

    public function mount(ConnectionType $connection, string $filter): void
    {
        $this->amount = 10;
        $this->filter = $filter;
        $this->page = 1;
        $this->connection = $connection;
        $this->reload();
    }

    private function reload()
    {
        $data = match ($this->connection) {
            ConnectionType::Direct => $this->cameraRepository->findByDirectConnection($this->filter, $this->amount, $this->page),
            ConnectionType::Simple => $this->cameraRepository->findBySimpleConnection($this->filter, $this->amount, $this->page),
//            ConnectionType::SlaveSwitch => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
//            ConnectionType::SlaveModem => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
//            ConnectionType::Full => $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page),
        };

        $this->reloadData($data);
    }

    /**
     * Get change table event name
     * @return string
     */
    public function getChangeTableEventName(): string
    {
        return static::CHANGE.'_'.$this->connection->name;
    }

    /**
     * Get show detail event name
     * @return string
     */
    private function getShowDetailEventName(): string
    {
        return static::DETAIL.'_'.$this->connection->name;
    }

    #[LiveAction]
    public function status(): void
    {
        $this->reload();
        $this->emit($this->getChangeTableEventName());
    }

    #[LiveAction]
    public function disconnect(#[LiveArg] Camera $camera): void
    {
        $camera->disconnect();
        $this->cameraRepository->save($camera, true);

        $this->reload();
        $this->emit($this->getChangeTableEventName());
    }

}