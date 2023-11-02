<?php

namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionModem\ConnectionModemDetail;
use App\Entity\Modem;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_modem/detail.html.twig')]
class ConnectionSlaveModemDetail extends ConnectionModemDetail
{
    use ComponentToolsTrait;

    const DEACTIVATE = self::class.'_deactivate';
    const DISCONNECT = self::class.'_disconnect';
    const CONNECT = self::class.'_connect';
    const ACTIVATE = self::class.'_activate';

    #[LiveProp(updateFromParent: true)]
    public ?Modem $masterModem = null;

    #[LiveListener(ConnectionSlaveModemTable::DETAIL.'_SlaveModem')]
    public function onConnectionSlaveModemTableDetailSlaveModem(#[LiveArg] Modem $entity): void
    {
        if(isset($this->modem['id'])){
            if($this->modem['id'] !== $entity->getId()){
                $this->modem = $this->details($entity);
                $this->active = $this->modem['state'];
            }
        }else{
            $this->modem = $this->details($entity);
            $this->active = $this->modem['state'];
        }
    }

}