<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionCommutator\CommutatorTable;
use App\Components\Live\ConnectionCommutator\PortList;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use App\Repository\CameraRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

#[AsLiveComponent(template: 'components/live/connection_camera/camera_table.html.twig')]
class CameraTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE_TABLE = 'camera_table:change:table';
    const SHOW_DETAIL = 'camera_table:show:detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    #[LiveProp]
    public ?Modem $modem = null;

    #[LiveProp(updateFromParent: true)]
    public ?Commutator $commutator = null;

    public function __construct(private readonly CameraRepository $cameraRepository)
    {
    }

    //cuando se monta por primera vez el componete
    public function mount(ConnectionType $connection, Port $port): void
    {
        $this->entityId = null;
        $this->connection = $connection;
        $this->port = $port;
        $this->filter = ($port->hasConnectedCamera()) ? $port->getEquipment()->getIp() : '';
        //$this->changeFilter();
        $this->reload();
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->entityId = null;
        $this->page = 1;
        if($this->port->hasConnectedCamera()){
            $this->filter = $this->port->getEquipment()->getIp();
            $this->reload();
        }else{
            $this->filter = '';
            $data = $this->cameraRepository->findCamerasByCommutator($this->port->getCommutator());
            $this->reloadData($data);
        }
    }

    private function reload()
    {
        //cambiar la forma en la que se buscan los datos
        if($this->port->hasConnectedCamera()){
            $data = $this->cameraRepository->findCameras($this->filter, $this->amount, $this->page);
        }else{
            $data = $this->cameraRepository->findCamerasByCommutator($this->port->getCommutator(), $this->filter, $this->amount, $this->page);
        }
        $this->reloadData($data);
    }

    /*//ver si lo puedo emitir directamente desde twig
    #[LiveAction]
    public function detail(#[LiveArg] int $cameraId): void
    {
        $this->emit(static::SHOW_DETAIL.':'.$this->connection->name, [
            'camera' => $cameraId
        ]);
    }*/

    /**
     * When save new commutator table filer by it
     * @param Camera $camera
     * @return void
     */
    #[LiveListener(NewCamera::FORM_SUCCESS.':Direct')]
    public function onFormSuccessDirect(#[LiveArg] Camera $camera): void
    {
        $this->filter = $camera->getIp();
        $this->changeFilter();
    }

    /**
     * Get change table event name
     * @return string
     */
    private function getChangeTableEventName(): string
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

    /*#[LiveListener(PortList::SELECTED_PORT.':Direct')]
    public function onPortListSelectedPortDirect(#[LiveArg] ?Port $port): void
    {
        $this->port = $port;
        $this->filter = ($port->hasConnectedCamera()) ? $port->getEquipment()->getIp() : '';
        $this->changeFilter();
    }*/



    /**
     * Update table from filter, amount or page just in direct connections
     * @return void

    #[LiveListener(CommutatorTable::SHOW_DETAIL.':Direct')]
    public function onCommutatorTableShowDetailDirect(): void
    {
        $this->port = null;
        $this->modem = null;
    }*/
}