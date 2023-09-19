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

    public function __construct(protected readonly CommutatorRepository $commutatorRepository)
    {
    }

    protected function reload()
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
    protected function onConnectionCommutatorNewFormSuccess(Commutator $commutator): void
    {
        $this->filter = $commutator->getIp();
        $this->changeFilter();
    }

    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_Direct')]
    public function onConnectionCommutatorNewFormSuccessDirect(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_Simple')]
    public function onConnectionCommutatorNewFormSuccessSimple(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_SlaveSwitch')]
    public function onConnectionCommutatorNewFormSuccessSlaveSwitch(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_SlaveModem')]
    public function onConnectionCommutatorNewFormSuccessSlaveModem(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

    #[LiveListener(ConnectionCommutatorNew::FORM_SUCCESS.'_Full')]
    public function onConnectionCommutatorNewFormSuccessFull(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

    /**
     * Get change table event name
     * @return string
     */
    protected function getChangeTableEventName(): string
    {
        return static::CHANGE.'_'.$this->connection->name;
    }

    /**
     * Get show detail event name
     * @return string
     */
    protected function getShowDetailEventName(): string
    {
        return static::DETAIL.'_'.$this->connection->name;
    }

    public function onConnectionCommutatorDetailEditInlineSave(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_Direct')]
    public function onConnectionCommutatorDetailEditInlineSaveDirect(): void
    {
        $this->onConnectionCommutatorDetailEditInlineSave();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_Simple')]
    public function onConnectionCommutatorDetailEditInlineSaveSimple(): void
    {
        $this->onConnectionCommutatorDetailEditInlineSave();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_SlaveSwitch')]
    public function onConnectionCommutatorDetailEditInlineSlaveSwitch(): void
    {
        $this->onConnectionCommutatorDetailEditInlineSave();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_SlaveModem')]
    public function onConnectionCommutatorDetailEditInlineSlaveModem(): void
    {
        $this->onConnectionCommutatorDetailEditInlineSave();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_COMMUTATOR.'_Full')]
    public function onConnectionCommutatorDetailEditInlineFull(): void
    {
        $this->onConnectionCommutatorDetailEditInlineSave();
    }

}