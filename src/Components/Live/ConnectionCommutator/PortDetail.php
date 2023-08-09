<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Port;
use App\Repository\PortRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/port_detail.html.twig')]
class PortDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    const SELECTED_PORT = 'port_detail:selected:port';

    #[LiveProp]
    public ?bool $active = null;

    #[LiveProp(updateFromParent: true)]
    public ?array $port = null;

    #[LiveProp]
    public bool $isEditing = false;

    #[LiveProp(updateFromParent: true)]
    public bool $selected = false;

    #[LiveProp(writable: true )]
    public ?string $data = null;

    public function mount($port)
    {
        $this->port = $port;
        $this->active = $port['state'];
    }

    #[LiveAction]
    public function activateEditing(): void
    {
        $this->isEditing = true;
    }

    #[LiveAction]
    public function select(#[LiveArg] Port $port): void
    {
        $this->selected = true;
        $this->emit(static::SELECTED_PORT, [
            'port' => $port->getId(),
        ]);
    }

    #[LiveAction]
    public function activate(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        $port->activate();
        $portRepository->save($port, true);
        $this->active = true;
    }

    #[LiveAction]
    public function deactivate(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        $port->deactivate();
        $portRepository->save($port, true);
        $this->active = false;
    }

    #[LiveAction]
    public function save(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        if(empty($this->data)){
            $this->data = 1;
        }
        $port->configure($this->data);
        $portRepository->save($port, true);
        $this->isEditing = false;

    }

}