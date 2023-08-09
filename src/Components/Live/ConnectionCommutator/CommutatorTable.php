<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\Traits\ComponentTable;
use App\Entity\Commutator;
use App\Repository\CommutatorRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
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

    public function __construct(private CommutatorRepository $commutatorRepository)
    {
    }

    private function reload()
    {
        $data = $this->commutatorRepository->findCommutator($this->filter, $this->amount, $this->page);
        $this->reloadData($data);
    }

    //ver si lo puedo emitir directamente desde twig
    #[LiveAction]
    public function detail(#[LiveArg] int $commutatorId): void
    {
        $this->emit(static::SHOW_DETAIL, [
            'commutator' => $commutatorId
        ]);
    }

    /**
     * When save new commutator table filer by it
     * @param Commutator $commutator
     * @return void
     */
    #[LiveListener(NewCommutatorForm::FORM_SUCCESS)]
    public function onFormSuccess(#[LiveArg] Commutator $commutator): void
    {
        $this->filter = $commutator->getIp();
        $this->changeFilter();
    }

    /**
     * Get change table event name
     * @return string
     */
    private function getChangeTableEventName(): string
    {
        return static::CHANGE_TABLE;
    }
}