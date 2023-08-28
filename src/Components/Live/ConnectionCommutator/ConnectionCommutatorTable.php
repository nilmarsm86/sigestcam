<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CommutatorRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/table.html.twig')]
class ConnectionCommutatorTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(private readonly CommutatorRepository $commutatorRepository)
    {
    }

    private function reload()
    {
        $this->entityId = null;
        $data = $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page);
        $this->reloadData($data);
    }

    /**
     * When save new commutator table filer by it
     * @param Commutator $commutator
     * @return void
     */
    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_Direct')]
    public function onConnectionCommutatorNewFormSuccessDirect(#[LiveArg] Commutator $commutator): void
    {
        $this->filter = $commutator->getIp();
        $this->changeFilter();
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

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_Direct')]
    public function onConnectionCommutatorDetailEditInlineSaveDirect(): void
    {
        $this->reload();
    }
}