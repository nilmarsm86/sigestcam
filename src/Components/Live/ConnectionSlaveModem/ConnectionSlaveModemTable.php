<?php

namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionCamera\ConnectionCameraDetail;
use App\Components\Live\ConnectionCamera\ConnectionCameraNew;
use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\ConnectionModem\ConnectionModemTable;
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

#[AsLiveComponent(template: 'components/live/connection_slave_modem/table.html.twig')]
class ConnectionSlaveModemTable extends ConnectionModemTable
{
//    use DefaultActionTrait;
    use ComponentToolsTrait;
//    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

//    #[LiveProp]
//    public ?ConnectionType $connection = null;

//    #[LiveProp(updateFromParent: true)]
//    public ?Port $port = null;

//    #[LiveProp]
//    public ?Modem $modem = null;

//    #[LiveProp(updateFromParent: true)]
//    public ?Commutator $commutator = null;

    #[LiveProp(updateFromParent: true)]
    public ?Modem $masterModem = null;

    #[LiveProp(updateFromParent: true)]
    public bool $inactives = false;

//    public function __construct(private readonly ModemRepository $modemRepository, private CameraRepository $cameraRepository)
//    {
//    }

    //cuando se monta por primera vez el componete
    public function mount(ConnectionType $connection, Port $port = null, Modem $masterModem = null, bool $inactives = false): void
    {
        $this->connection = $connection;
        $this->port = $port;
        $this->masterModem = $masterModem;
        $this->inactives = $inactives;
        $this->filterAndReload();
    }

//    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
//    public function __invoke(): void
//    {
//        $this->page = 1;
//        $this->filterAndReload();
//    }

    protected function filterAndReload(): void
    {
        $this->entityId = null;
        //$this->filter = ($this->masterModem->hasConnectedModem()) ? $this->port->getEquipment()->getIp() : '';
        $this->filter = '';
        $this->reload();
    }

    protected function reload(): void
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }

        //cambiar la forma en la que se buscan los datos
        if($this->inactives === false){
            if(!is_null($this->masterModem->getId())){
                $data = $this->modemRepository->findModemByMaster($this->masterModem, $this->filter, $this->amount, $this->page);
            }else{
                //TODO: no deberia utilizar este ip quemado
                $data = $this->modemRepository->findInactiveModemsWithoutPortAndMasterModem('0.0.0.0', $this->filter, $this->amount, $this->page);
            }
        }else{
            $data = $this->modemRepository->findInactiveModemsWithoutPortAndMasterModem($this->masterModem->getIp(), $this->filter, $this->amount, $this->page);
        }

//        if(!is_null($this->masterModem->getId())){
//            $data = $this->modemRepository->findModemByMaster($this->masterModem, $this->filter, $this->amount, $this->page);
//        }else{
//            dump($this->masterModem);
//            $data = $this->modemRepository->findInactiveModemsWithoutPortAndMasterModem($this->masterModem->getIp(), $this->filter, $this->amount, $this->page);
//        }
        $this->reloadData($data);
        $this->setCamerasAmount();
    }

//    //mejorar
//    private function setCamerasAmount()
//    {
//        for($i=0;$i<count($this->data);$i++){
//            $this->data[$i]['cameras'] = $this->cameraRepository->findAmountCamerasByModemId($this->data[$i]['id']);
//        }
//    }

    /**
     * When save new modem table filer by it
     * @param Modem $modem
     * @return void
     */
    #[LiveListener(ConnectionSlaveModemNew::FORM_SUCCESS.'_SlaveModem')]
    public function onConnectionSlaveModemNewFormSuccessSlaveModem(#[LiveArg] Modem $modem): void
    {
        $this->filter = $modem->getIp();
        $this->inactives = false;
        $this->changeFilter();
    }

//    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Simple')]
//    public function onConnectionCameraNewFormSuccessSimple(#[LiveArg] Camera $camera): void
//    {
//        $this->filterAndReload();
//    }

//    /**
//     * Get change table event name
//     * @return string
//     */
//    protected function getChangeTableEventName(): string
//    {
//        return static::CHANGE.'_'.$this->connection->name;
//    }

//    /**
//     * Get show detail event name
//     * @return string
//     */
//    protected function getShowDetailEventName(): string
//    {
//        return static::DETAIL.'_'.$this->connection->name;
//    }

//    #[LiveListener(ConnectionDetailEditInline::SAVE_MODEM.'_Simple')]
//    public function onConnectionDetailEditInlineSaveModemSimple(): void
//    {
//        $this->reload();
//    }

//    #[LiveListener(ConnectionModemDetail::ACTIVATE.'_Simple')]
//    public function onConnectionModemDetailActivateSimple(): void
//    {
//        $this->reload();
//    }

//    #[LiveListener(ConnectionModemDetail::DEACTIVATE.'_Simple')]
//    public function onConnectionModemDetailDeactivateSimple(): void
//    {
//        $this->reload();
//    }

//    #[LiveListener(ConnectionCameraDetail::DISCONNECT.'_Simple')]
//    public function onConnectionCameraDetailDisconnectSimple(#[LiveArg] Modem $modem): void
//    {
//        //TODO que hacer cuando se desconecta una camara de un modem
//        $this->reload();
//    }

//    #[LiveListener(ConnectionCameraDetail::CONNECT.'_Simple')]
//    public function onConnectionCameraDetailConnectSimple(#[LiveArg] Modem $modem): void
//    {
//        //TODO que hacer cuando se conecta una camara de un modem
//        $this->reload();
//    }

}