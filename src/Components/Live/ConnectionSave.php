<?php

namespace App\Components\Live;

use App\Components\Live\ConnectionCamera\ConnectionCameraTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionModem\ConnectionModemTable;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_save.html.twig')]
class ConnectionSave extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?Camera $camera = null;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Modem $modem = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function onConnectionCameraTableDetail(Camera $entity): void
    {
        $this->camera = $entity;
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Direct')]
    public function onConnectionCameraTableDetailDirect(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Simple')]
    public function onConnectionCameraTableDetailSimple(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCameraTableDetailSlaveSwitch(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_SlaveModem')]
    public function onConnectionCameraTableDetailSlaveModem(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Full')]
    public function onConnectionCameraTableDetailFull(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    public function onConnectionCameraTableChange(): void
    {
        $this->camera = null;
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_Direct')]
    public function onConnectionCameraTableChangeDirect(): void
    {
        $this->onConnectionCameraTableChange();
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_Simple')]
    public function onConnectionCameraTableChangeSimple(): void
    {
        $this->onConnectionCameraTableChange();
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionCameraTableChangeSlaveSwitch(): void
    {
        $this->onConnectionCameraTableChange();
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_SlaveModem')]
    public function onConnectionCameraTableChangeSlaveModem(): void
    {
        $this->onConnectionCameraTableChange();
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_Full')]
    public function onConnectionCameraTableChangeFull(): void
    {
        $this->onConnectionCameraTableChange();
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(): Response
    {
        if(!$this->camera->isActive()){
            $this->camera->activate();
        }

        if($this->port){
            $this->camera->setPort($this->port);
        }

        if($this->modem){
            $this->camera->setModem($this->modem);
        }
        $this->entityManager->persist($this->camera);

        if($this->modem){
            if(!$this->modem->isActive()){
                $this->modem->activate();
            }
            $this->entityManager->persist($this->modem);
        }

        if(!$this->port){
            $this->port = $this->modem->getPort();
        }

        $this->port->setConnectionType($this->connection);
        if(!$this->port->isActive()){
            $this->port->activate();
        }
        $this->entityManager->persist($this->port);

        $commutator = $this->port->getCommutator();
        if(!$commutator->isActive()){
            $commutator->activate();
        }
        $this->entityManager->persist($commutator);

        $this->entityManager->flush();

        $this->addFlash('success', 'Se a registrado una nueva conexión directa en el sistema.');
        return $this->redirectToRoute('connection_direct_list', ['filter' => $this->camera->getIp()]);
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    public function onConnectionCommutatorTableChange(): void
    {
        $this->camera = null;
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Simple')]
    public function onConnectionCommutatorTableChangeSimple(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveSwitch')]
    public function onConnectionCommutatorTableChangeSlaveSwitch(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_SlaveModem')]
    public function onConnectionCommutatorTableChangeSlaveModem(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Full')]
    public function onConnectionCommutatorTableChangeFull(): void
    {
        $this->onConnectionCommutatorTableChange();
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    public function onConnectionCommutatorTableDetail(): void
    {
        $this->camera = null;
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Direct')]
    public function onConnectionCommutatorTableDetailDirect(): void
    {
        $this->onConnectionCommutatorTableDetail();
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Simple')]
    public function onConnectionCommutatorTableDetailSimple(): void
    {
        $this->onConnectionCommutatorTableDetail();
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCommutatorTableDetailSlaveSwitch(): void
    {
        $this->onConnectionCommutatorTableDetail();
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveModem')]
    public function onConnectionCommutatorTableDetailSlaveModem(): void
    {
        $this->onConnectionCommutatorTableDetail();
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Full')]
    public function onConnectionCommutatorTableDetailFull(): void
    {
        $this->onConnectionCommutatorTableDetail();
    }

    public function onConnectionCommutatorPortListSelected(?Port $port): void
    {
        $this->camera = null;
        $this->port = $port;
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Direct')]
    public function onConnectionCommutatorPortListSelectedDirect(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Simple')]
    public function onConnectionCommutatorPortListSelectedSimple(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveSwitch')]
    public function onConnectionCommutatorPortListSelectedSlaveSwitch(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_SlaveModem')]
    public function onConnectionCommutatorPortListSelectedSlaveModem(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Full')]
    public function onConnectionCommutatorPortListSelectedFull(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionCommutatorPortListSelected($port);
    }

    #[LiveListener(ConnectionModemTable::CHANGE.'_Simple')]
    public function onConnectionModemTableChangeSimple(): void
    {
        $this->camera = null;
        $this->modem = null;
        $this->port = null;
    }

    #[LiveListener(ConnectionModemTable::DETAIL.'_Simple')]
    public function onConnectionModemTableDetailSimple(#[LiveArg] ?Modem $modem): void
    {
        $this->camera = null;
        $this->modem = $modem;
        $this->port = null;
    }

}