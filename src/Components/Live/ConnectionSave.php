<?php

namespace App\Components\Live;

use App\Components\Live\ConnectionCamera\ConnectionCameraTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
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

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Direct')]
    public function onConnectionCameraTableDetailDirect(#[LiveArg] Camera $entity): void
    {
        $this->camera = $entity;
    }

    #[LiveListener(ConnectionCameraTable::CHANGE.'_Direct')]
    public function onConnectionCameraTableChangeDirect(): void
    {
        $this->camera = null;
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
        $this->camera->setPort($this->port);
        $this->entityManager->persist($this->camera);

        //$port = $this->camera->getPort();
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
    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->camera = null;
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Direct')]
    public function onConnectionCommutatorTableDetailDirect(): void
    {
        $this->camera = null;
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Direct')]
    public function onConnectionCommutatorPortListSelectedDirect(#[LiveArg] ?Port $port): void
    {
        $this->camera = null;
        $this->port = $port;
    }

}