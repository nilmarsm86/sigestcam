<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Commutator;
use App\Repository\CommutatorRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/commutator_detail.html.twig')]
class CommutatorDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    const DEACTIVATE_SWITCH = 'switch_detail:deactivate:switch';

    #[LiveProp(updateFromParent: true)]
    public ?array $commutator = null;

    #[LiveProp]
    public ?bool $active = null;

    public function mount(?array $commutator = null): void
    {
        $this->active = $commutator['state'];
        $this->commutator = $commutator;
    }

    #[LiveAction]
    public function activate(CommutatorRepository $commutatorRepository, #[LiveArg] Commutator $commutator): void
    {
        $commutator->activate();
        $commutatorRepository->save($commutator, true);
        $this->active = true;
    }

    #[LiveAction]
    public function deactivate(CommutatorRepository $commutatorRepository, #[LiveArg] Commutator $commutator): void
    {
        $commutator->deactivate();
        $commutatorRepository->save($commutator, true);
        $this->active = false;

        $this->emitUp(static::DEACTIVATE_SWITCH, [
            'commutator' => $commutator->getId(),
        ]);
    }


}