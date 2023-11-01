<?php

namespace App\Components\Live\ConnectionModem;

use App\Components\Live\ConnectionCamera\ConnectionCameraDetail;
use App\Components\Live\ConnectionCamera\ConnectionCameraNew;
use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use App\Repository\CameraRepository;
use App\Repository\ModemRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_modem/table.html.twig')]
class ConnectionModemTable
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

//    #[LiveProp(updateFromParent: true)]
//    public bool $inactives = false;

    public function __construct(protected readonly ModemRepository $modemRepository, protected CameraRepository $cameraRepository)
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

    protected function filterAndReload(): void
    {
        $this->entityId = null;
        $this->filter = ($this->port->hasConnectedModem()) ? $this->port->getEquipment()->getIp() : '';
        $this->reload();
    }

    protected function reload(): void
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }

//        if($this->inactives === false){
            //cambiar la forma en la que se buscan los datos
            if($this->port->hasConnectedModem()){
                $data = $this->modemRepository->findModemByPort($this->port, $this->filter, $this->amount, $this->page);
            }else{
                $data = $this->modemRepository->findInactiveModemsWithoutPort($this->filter, $this->amount, $this->page);
            }
//        }else{
//            if($this->port->hasConnectedModem()){
//                $data = $this->modemRepository->findModemByPort($this->port, $this->filter, $this->amount, $this->page);
//            }else{
//                $data = $this->modemRepository->findInactiveModemsWithoutPort($this->filter, $this->amount, $this->page);
//            }
//        }
        $this->reloadData($data);
        $this->setCamerasAmount();
        if($this->connection->name === ConnectionType::SlaveModem->name){
            $this->setModemsAmount();
        }

    }

    //mejorar
    protected function setCamerasAmount()
    {
        for($i=0;$i<count($this->data);$i++){
            $this->data[$i]['cameras'] = $this->cameraRepository->findAmountCamerasByModemId($this->data[$i]['id']);
        }
    }

    //mejorar
    protected function setModemsAmount()
    {
        for($i=0;$i<count($this->data);$i++){
            $this->data[$i]['modems'] = $this->modemRepository->findAmountSlaveModemsByMasterModemId($this->data[$i]['id']);
        }
    }

    /**
     * When save new modem, table filer by it
     * @param Modem $modem
     * @return void
     */
    public function onConnectionModemNewFormSuccess(Modem $modem): void
    {
        $this->filter = $modem->getIp();
        $this->changeFilter();
    }

    #[LiveListener(ConnectionModemNew::FORM_SUCCESS.'_Simple')]
    public function onConnectionModemNewFormSuccessSimple(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionModemNewFormSuccess($modem);
    }

    #[LiveListener(ConnectionModemNew::FORM_SUCCESS.'_SlaveModem')]
    public function onConnectionModemNewFormSuccessSlaveModem(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionModemNewFormSuccess($modem);
    }

    #[LiveListener(ConnectionModemNew::FORM_SUCCESS.'_Full')]
    public function onConnectionModemNewFormSuccessFull(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionModemNewFormSuccess($modem);
    }

    //PORQUE ESTA AQUI
    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Simple')]
    public function onConnectionCameraNewFormSuccessSimple(#[LiveArg] Camera $camera): void
    {
        $this->filterAndReload();
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

    protected function onConnectionDetailEditInlineSaveModem(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_MODEM.'_Simple')]
    public function onConnectionDetailEditInlineSaveModemSimple(): void
    {
        $this->onConnectionDetailEditInlineSaveModem();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_MODEM.'_SlaveModem')]
    public function onConnectionDetailEditInlineSaveModemSlaveModem(): void
    {
        $this->onConnectionDetailEditInlineSaveModem();
    }

    public function onConnectionModemDetailActivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionModemDetail::ACTIVATE.'_Simple')]
    public function onConnectionModemDetailActivateSimple(): void
    {
        $this->onConnectionModemDetailActivate();
    }

    #[LiveListener(ConnectionModemDetail::ACTIVATE.'_SlaveModem')]
    public function onConnectionModemDetailActivateSlaveModem(): void
    {
        $this->onConnectionModemDetailActivate();
    }

    public function onConnectionModemDetailDeactivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionModemDetail::DEACTIVATE.'_Simple')]
    public function onConnectionModemDetailDeactivateSimple(): void
    {
        $this->onConnectionModemDetailDeactivate();
    }

    #[LiveListener(ConnectionModemDetail::DEACTIVATE.'_SlaveModem')]
    public function onConnectionModemDetailDeactivateSlaveModem(): void
    {
        $this->onConnectionModemDetailDeactivate();
    }

    public function onConnectionCameraDetailDisconnect(Modem $modem): void
    {
        //TODO que hacer cuando se desconecta una camara de un modem
        $this->reload();
    }

    #[LiveListener(ConnectionCameraDetail::DISCONNECT.'_Simple')]
    public function onConnectionCameraDetailDisconnectSimple(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionCameraDetailDisconnect($modem);
    }

    #[LiveListener(ConnectionCameraDetail::DISCONNECT.'_SlaveModem')]
    public function onConnectionCameraDetailDisconnectSlaveModem(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionCameraDetailDisconnect($modem);
    }

    public function onConnectionCameraDetailConnect(Modem $modem): void
    {
        //TODO que hacer cuando se conecta una camara de un modem
        $this->reload();
    }

    #[LiveListener(ConnectionCameraDetail::CONNECT.'_Simple')]
    public function onConnectionCameraDetailConnectSimple(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionCameraDetailConnect($modem);
    }

    #[LiveListener(ConnectionCameraDetail::CONNECT.'_SlaveModem')]
    public function onConnectionCameraDetailConnectSlaveModem(#[LiveArg] Modem $modem): void
    {
        $this->onConnectionCameraDetailConnect($modem);
    }

}