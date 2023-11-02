<?php

namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionModem\ConnectionModemTable;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_modem/table.html.twig')]
class ConnectionSlaveModemTable extends ConnectionModemTable
{
    use ComponentToolsTrait;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp(updateFromParent: true)]
    public ?Modem $masterModem = null;

    #[LiveProp(updateFromParent: true)]
    public bool $inactives = false;

    /**
     * cuando se monta por primera vez el componete
     * @param ConnectionType $connection
     * @param Port|null $port
     * @param Modem|null $masterModem
     * @param bool $inactives
     * @return void
     */
    public function mount(ConnectionType $connection, Port $port = null, Modem $masterModem = null, bool $inactives = false): void
    {
        $this->connection = $connection;
        $this->port = $port;
        $this->masterModem = $masterModem;
        $this->inactives = $inactives;
        $this->filterAndReload();
    }

    /**
     * @return void
     */
    protected function filterAndReload(): void
    {
        $this->entityId = null;
        //$this->filter = ($this->masterModem->hasConnectedModem()) ? $this->port->getEquipment()->getIp() : '';
        $this->filter = '';
        $this->reload();
    }

    /**
     * @return void
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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
            //TODO si el masterModem no tiene slaveModems entonces muestro todos los inactivos
        }

        $this->reloadData($data);
        $this->setCamerasAmount();
    }

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

}