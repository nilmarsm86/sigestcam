<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/conecction_commutator.html.twig')]
class ConnectionCommutator
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?Commutator $commutator = null;

    #[LiveProp(writable: true)]
    public ?ConnectionType $connection = null;

}