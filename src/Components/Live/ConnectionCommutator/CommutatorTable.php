<?php

namespace App\Components\Live\ConnectionCommutator;

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

#[AsLiveComponent(template: 'components/live/connection_commutator/commutator_table.html.twig')]
class CommutatorTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE_TABLE = 'commutator_table:change:table';
    const SHOW_DETAIL = 'commutator_table:show:detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(private CommutatorRepository $commutatorRepository)
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
    #[LiveListener(NewCommutatorForm::FORM_SUCCESS.':Direct')]
    public function onFormSuccessDirect(#[LiveArg] Commutator $commutator): void
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
        return static::CHANGE_TABLE.':'.$this->connection->name;
    }

    /**
     * Get show detail event name
     * @return string
     */
    private function getShowDetailEventName(): string
    {
        return static::SHOW_DETAIL.':'.$this->connection->name;
    }

    /**
     * When save new commutator table filer by it
     * @param Commutator $commutator
     * @return void
     */
    #[LiveListener(NewCommutatorForm::FORM_SUCCESS.':Direct')]
    public function onNewCommutatorFormSuccessDirect(#[LiveArg] Commutator $commutator): void
    {
        $this->filter = $commutator->getIp();
        $this->changeFilter();
    }

    #[LiveListener(CommutatorDetailEditInline::SAVE.':Direct')]
    public function onCommutatorDetailEditInlineSaveDirect(): void
    {
        $this->reload();
    }
}