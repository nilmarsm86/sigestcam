<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use App\Repository\CameraRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/table.html.twig')]
class ConnectionCameraTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

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
        $this->connection = $connection;
        $this->port = $port;
        $this->filterAndReload();
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->page = 1;
        $this->filterAndReload();
    }

    private function filterAndReload(): void
    {
        $this->entityId = null;
        $this->filter = ($this->port->hasConnectedCamera()) ? $this->port->getEquipment()->getIp() : '';
        $this->reload();
    }

    private function reload(): void
    {
        //cambiar la forma en la que se buscan los datos
        if($this->port->hasConnectedCamera()){
//            if($this->port->isActive()){
//                $data = $this->cameraRepository->findActiveCamerasWithPort($this->filter, $this->amount, $this->page);
//            }else{
//                $data = $this->cameraRepository->findInactiveCamerasWithPort($this->filter, $this->amount, $this->page);
//            }
            $data = $this->cameraRepository->findCameraByPort($this->port, $this->filter, $this->amount, $this->page);
        }else{
            $data = $this->cameraRepository->findInactiveCamerasWithoutPort($this->filter, $this->amount, $this->page);
        }
        $this->reloadData($data);
    }

    /**
     * When save new commutator table filer by it
     * @param Camera $camera
     * @return void
     */
    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Direct')]
    public function onConnectionCameraNewFormSuccessDirect(#[LiveArg] Camera $camera): void
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

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_Direct')]
    public function onConnectionCameraDetailEditInlineSaveDirect(): void
    {
        $this->reload();
    }

}