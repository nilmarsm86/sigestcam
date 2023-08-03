<?php

namespace App\Components\Live;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CommutatorRepository;
use http\Message;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[AsLiveComponent(template: 'components/live/switch_detail.html.twig')]
class SwitchDetail
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?array $commutator = null;

    #[LiveAction]
    public function activate(CommutatorRepository $commutatorRepository, #[LiveArg] Commutator $commutator): void
    {
        $commutator->activate();
        $commutatorRepository->save($commutator, true);
    }

    #[LiveAction]
    public function deactivate(CommutatorRepository $commutatorRepository, #[LiveArg] Commutator $commutator): void
    {
        $commutator->deactivate();
        $commutatorRepository->save($commutator, true);
    }
}