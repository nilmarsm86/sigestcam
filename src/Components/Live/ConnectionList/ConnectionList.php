<?php

namespace App\Components\Live\ConnectionList;

use App\Entity\Enums\ConnectionType;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_list/connection_list.html.twig')]
class ConnectionList
{
    use DefaultActionTrait;

    #[LiveProp]
    public string $title = '';

    #[LiveProp]
    public string $newHref = '';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?string $filter = '';
}