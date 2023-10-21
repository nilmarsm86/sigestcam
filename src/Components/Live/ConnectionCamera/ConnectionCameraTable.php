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

    #[LiveProp(updateFromParent: true)]
    public ?Modem $modem = null;

    #[LiveProp(updateFromParent: true)]
    public ?Commutator $commutator = null;

    public function __construct(private readonly CameraRepository $cameraRepository)
    {
    }

    //cuando se monta por primera vez el componete
    public function mount(ConnectionType $connection, Port $port, ?Modem $modem = null): void
    {
        $this->connection = $connection;
        $this->port = $port;
        $this->modem = $modem;
        $this->filterAndReload();
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->page = 1;
        $this->filterAndReload();
        $this->emit($this->getChangeTableEventName());
    }

    private function filterAndReload(): void
    {
        $this->entityId = null;
        $this->filter = ($this->port->hasConnectedCamera()) ? $this->port->getEquipment()->getIp() : '';
        $this->reload();
    }

    private function reload(): void
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }

        //cambiar la forma en la que se buscan los datos
        if($this->connection->name === ConnectionType::Direct->name){
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
        }

        if($this->connection->name === ConnectionType::Simple->name || $this->connection->name === ConnectionType::SlaveModem->name){
            if(!is_null($this->modem) && !is_null($this->modem->getId())){
                $data = $this->cameraRepository->findCameraByModem($this->modem, $this->filter, $this->amount, $this->page);
            }else{
                $data = $this->cameraRepository->findInactiveCamerasWithoutPortAndModem($this->filter, $this->amount, $this->page);
            }
        }

        if($this->connection->name === ConnectionType::SlaveSwitch->name){
            if($this->port->hasConnectedCamera()){
                $data = $this->cameraRepository->findCameraByPortAndNotModem($this->port, $this->filter, $this->amount, $this->page);
            }else{
                $data = $this->cameraRepository->findInactiveCamerasWithoutPort($this->filter, $this->amount, $this->page);
            }
        }

//        if($this->connection->name === ConnectionType::SlaveModem->name){
//            if(!is_null($this->modem->getId())){
//                $data = $this->cameraRepository->findCameraByModem($this->modem, $this->filter, $this->amount, $this->page);
//            }else{
//                $data = $this->cameraRepository->findInactiveCamerasWithoutPortAndModem($this->filter, $this->amount, $this->page);
//            }
//        }

        $this->reloadData($data);
    }

    /**
     * When save new commutator table filer by it
     * @param Camera $camera
     * @return void
     */
    private function onConnectionCameraNewFormSuccess(Camera $camera): void
    {
        $this->filter = $camera->getIp();
        $this->changeFilter();
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Direct')]
    public function onConnectionCameraNewFormSuccessDirect(#[LiveArg] Camera $camera): void
    {
        $this->onConnectionCameraNewFormSuccess($camera);
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Simple')]
    public function onConnectionCameraNewFormSuccessSimple(#[LiveArg] Camera $camera): void
    {
        $this->onConnectionCameraNewFormSuccess($camera);
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_SlaveSwitch')]
    public function onConnectionCameraNewFormSuccessSlaveSwitch(#[LiveArg] Camera $camera): void
    {
        $this->onConnectionCameraNewFormSuccess($camera);
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_SlaveModem')]
    public function onConnectionCameraNewFormSuccessSlaveModem(#[LiveArg] Camera $camera): void
    {
        $this->onConnectionCameraNewFormSuccess($camera);
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Full')]
    public function onConnectionCameraNewFormSuccessFull(#[LiveArg] Camera $camera): void
    {
        $this->onConnectionCameraNewFormSuccess($camera);
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

    public function onConnectionDetailEditInlineSaveCamera(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_Direct')]
    public function onConnectionDetailEditInlineSaveCameraDirect(): void
    {
        $this->onConnectionDetailEditInlineSaveCamera();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_Simple')]
    public function onConnectionDetailEditInlineSaveCameraSimple(): void
    {
        $this->onConnectionDetailEditInlineSaveCamera();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_SlaveSwitch')]
    public function onConnectionDetailEditInlineSaveCameraSlaveSwitch(): void
    {
        $this->onConnectionDetailEditInlineSaveCamera();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_SlaveModem')]
    public function onConnectionDetailEditInlineSaveCameraSlaveModem(): void
    {
        $this->onConnectionDetailEditInlineSaveCamera();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_CAMERA.'_Full')]
    public function onConnectionDetailEditInlineSaveCameraFull(): void
    {
        $this->onConnectionDetailEditInlineSaveCamera();
    }

    public function onConnectionCameraDetailActivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionCameraDetail::ACTIVATE.'_Direct')]
    public function onConnectionCameraDetailActivateDirect(): void
    {
        $this->onConnectionCameraDetailActivate();
    }

    #[LiveListener(ConnectionCameraDetail::ACTIVATE.'_Simple')]
    public function onConnectionCameraDetailActivateSimple(): void
    {
        $this->onConnectionCameraDetailActivate();
    }

    #[LiveListener(ConnectionCameraDetail::ACTIVATE.'_SlaveSwitch')]
    public function onConnectionCameraDetailActivateSlaveSwitch(): void
    {
        $this->onConnectionCameraDetailActivate();
    }

    #[LiveListener(ConnectionCameraDetail::ACTIVATE.'_SlaveModem')]
    public function onConnectionCameraDetailActivateSlaveModem(): void
    {
        $this->onConnectionCameraDetailActivate();
    }

    #[LiveListener(ConnectionCameraDetail::ACTIVATE.'_Full')]
    public function onConnectionCameraDetailActivateFull(): void
    {
        $this->onConnectionCameraDetailActivate();
    }

    public function onConnectionCameraDetailDeactivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionCameraDetail::DEACTIVATE.'_Direct')]
    public function onConnectionCameraDetailDeactivateDirect(): void
    {
        $this->onConnectionCameraDetailDeactivate();
    }

    #[LiveListener(ConnectionCameraDetail::DEACTIVATE.'_Simple')]
    public function onConnectionCameraDetailDeactivateSimple(): void
    {
        $this->onConnectionCameraDetailDeactivate();
    }

    #[LiveListener(ConnectionCameraDetail::DEACTIVATE.'_SlaveSwitch')]
    public function onConnectionCameraDetailDeactivateSlaveSwitch(): void
    {
        $this->onConnectionCameraDetailDeactivate();
    }

    #[LiveListener(ConnectionCameraDetail::DEACTIVATE.'_SlaveModem')]
    public function onConnectionCameraDetailDeactivateSlaveModem(): void
    {
        $this->onConnectionCameraDetailDeactivate();
    }

    #[LiveListener(ConnectionCameraDetail::DEACTIVATE.'_Full')]
    public function onConnectionCameraDetailDeactivateFull(): void
    {
        $this->onConnectionCameraDetailDeactivate();
    }

    #[LiveListener(ConnectionCameraDetail::CONNECT.'_Simple')]
    public function onConnectionCameraDetailConnectSimple(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionCameraDetail::DISCONNECT.'_Simple')]
    public function onConnectionCameraDetailDisconnectSimple(): void
    {
        $this->reload();
    }

}