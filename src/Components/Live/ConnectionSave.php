<?php

namespace App\Components\Live;

use App\Components\Live\ConnectionCamera\ConnectionCameraTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\ConnectionModem\ConnectionModemTable;
use App\Components\Live\ConnectionSlaveCommutator\ConnectionSlaveCommutatorPortList;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Card;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Msam;
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

    #[LiveProp]
    public ?Port $slavePort = null;

    #[LiveProp]
    public ?Card $card = null;

    #[LiveProp]
    public ?Msam $msam = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Camera $entity
     * @return void
     */
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
        $this->modem = $entity->getModem();
        $this->onConnectionCameraTableDetail($entity);
        $this->port = $this->getRealEntity($this->modem?->getPort());
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCameraTableDetailSlaveSwitch(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_SlaveModem')]
    public function onConnectionCameraTableDetailSlaveModem(#[LiveArg] Camera $entity): void
    {
        $this->modem = $entity->getModem();
        $this->onConnectionCameraTableDetail($entity);
        $this->port = $this->getRealEntity($this->modem?->getMasterModem()?->getPort());
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Full')]
    public function onConnectionCameraTableDetailFull(#[LiveArg] Camera $entity): void
    {
        $this->onConnectionCameraTableDetail($entity);
        $this->modem = $entity->getModem();
        $cardPort = $this->getRealEntity($this->modem?->getPort());
        $this->card = $cardPort->getCard();
        $this->msam = $this->card->getMsam();
        $this->port = $this->msam->getPort();
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
     * @return string
     * @throws Exception
     */
    protected function saveDirect(): string
    {
        if($this->port){
            if(is_null($this->modem)){
                $this->camera->setPort($this->port);

                if($this->port->isFromCommutator()){
                    $commutator = $this->port->getCommutator();
                    $this->camera->setMunicipality($commutator->getMunicipality());
                }
            }
        }

        $this->entityManager->persist($this->camera);

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

        return 'connection_direct_list';
    }

    protected function saveSimple(): string
    {
        if($this->port){
            if(is_null($this->modem)){
                $this->camera->setPort($this->port);

                if($this->port->isFromCommutator()){
                    $commutator = $this->port->getCommutator();
                    $this->camera->setMunicipality($commutator->getMunicipality());
                }
            }
        }

        if($this->modem){
            $this->camera->setModem($this->modem);
            if(!$this->modem->isActive()){
                $this->modem->activate();
            }
            $this->entityManager->persist($this->modem);
            $port = $this->modem->getPort();
            if($port->isFromCommutator()){
                $commutator = $this->port->getCommutator();
                $this->modem->setMunicipality($commutator->getMunicipality());
                $this->camera->setMunicipality($commutator->getMunicipality());
            }
        }
        $this->entityManager->persist($this->camera);

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

        return 'connection_simple_list';
    }

    protected function saveSlaveSwitch(): string
    {
        if($this->port && $this->slavePort){
            $masterPort = $this->port;
            $masterCommutator = $masterPort->getCommutator();
            $slavePort = $this->slavePort;
            $slaveCommutator = $slavePort->getCommutator();

            $slaveCommutator->setPort($masterPort);
            $this->camera->setPort($slavePort);

            $slaveCommutator->setMunicipality($masterCommutator->getMunicipality());
            $this->camera->setMunicipality($masterCommutator->getMunicipality());

            $masterPort->setConnectionType($this->connection);

            $masterCommutator->activate();
            $masterPort->activate();
            $slaveCommutator->activate();
            $slavePort->activate();

            $this->entityManager->persist($slaveCommutator);
            $this->entityManager->persist($slavePort);
            $this->entityManager->persist($this->camera);
        }

        return 'connection_slave_switch_list';
    }

    protected function saveSlaveModem(): string
    {
        if($this->port){
            if(is_null($this->modem)){
                $this->camera->setPort($this->port);

                if($this->port->isFromCommutator()){
                    $commutator = $this->port->getCommutator();
                    $this->camera->setMunicipality($commutator->getMunicipality());
                }
            }
        }

        if($this->modem){
            $this->camera->setModem($this->modem);
            if(!$this->modem->isActive()){
                $this->modem->activate();
            }
            $this->entityManager->persist($this->modem);

            if($masterModem = $this->modem->getMasterModem()){
                if(!$masterModem->isActive()){
                    $masterModem->activate();
                }
                $this->entityManager->persist($masterModem);
            }

            $port = $this->modem->getMasterModem()->getPort();
            if($port->isFromCommutator()){
                $commutator = $this->port->getCommutator();
                $this->modem->getMasterModem()->setMunicipality($commutator->getMunicipality());
                $this->camera->setMunicipality($commutator->getMunicipality());
            }
        }
        $this->entityManager->persist($this->camera);

        if(!$this->port){
            $this->port = $this->modem->getMasterModem()->getPort();
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

        return 'connection_slave_modem_list';
    }

    protected function saveFull(): string
    {
        if($this->modem){
            $this->camera->setModem($this->modem);
            if(!$this->modem->isActive()){
                $this->modem->activate();
            }
            $this->entityManager->persist($this->modem);
        }

        $port = $this->modem->getPort();
        if($port->isFromCard()){
            $msam = $port->getCard()->getMsam();
            if(!$msam->isActive()){
                $msam->activate();
            }
            $this->entityManager->persist($msam);
        }

        $this->entityManager->persist($this->camera);

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

        return 'connection_full_list';
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

        $redirect = match ($this->connection) {
            ConnectionType::Direct => $this->saveDirect(),
            ConnectionType::Simple => $this->saveSimple(),
            ConnectionType::SlaveSwitch => $this->saveSlaveSwitch(),
            ConnectionType::SlaveModem => $this->saveSlaveModem(),
            ConnectionType::Full => $this->saveFull(),
        };

        $this->entityManager->flush();

        $this->addFlash('success', 'Se a registrado una nueva conexión '.ConnectionType::getLabelFrom($this->connection).' en el sistema.');

        return $this->redirectToRoute($redirect, ['filter' => $this->camera->getIp()]);
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

    public function onConnectionSlaveCommutatorPortListSelected(?Port $port): void
    {
        $this->camera = null;
        $this->slavePort = $port;
    }

    #[LiveListener(ConnectionSlaveCommutatorPortList::SELECTED.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorPortListSelectedSlaveSwitch(#[LiveArg] ?Port $port): void
    {
        $this->onConnectionSlaveCommutatorPortListSelected($port);
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
        $this->port = $modem?->getPort();
    }

    /**
     * @param $proxy
     * @return mixed|object|null
     */
    public function getRealEntity($proxy)
    {
        if ($proxy instanceof \Doctrine\Persistence\Proxy) {
            $proxy_class_name = get_class($proxy);
            $class_name = $this->entityManager->getClassMetadata($proxy_class_name)->rootEntityName;
            $this->entityManager->detach($proxy);
            return $this->entityManager->find($class_name, $proxy->getId());
        }

        return $proxy;
    }

}