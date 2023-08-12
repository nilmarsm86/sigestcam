<?php

namespace App\Components\Live;

use App\Components\Live\ConnectionCamera\CameraTable;
use App\Components\Live\ConnectionCommutator\CommutatorTable;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
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

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[LiveListener(CameraTable::SHOW_DETAIL.':Direct')]
    public function onCameraTableShowDetailDirect(#[LiveArg] Camera $entity): void
    {
        $this->camera = $entity;
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

        $port = $this->camera->getPort();
        $port->setConnectionType($this->connection);
        if(!$port->isActive()){
            $port->activate();
        }

        $commutator = $port->getCommutator();
        if(!$commutator->isActive()){
            $commutator->activate();
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Se a registrado una nueva conexión directa en el sistema.');
        return $this->redirectToRoute('app_login');


    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    #[LiveListener(CommutatorTable::CHANGE_TABLE.':Direct')]
    public function onCommutatorTableChangeTableDirect(): void
    {
        $this->camera = null;
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void
     */
    #[LiveListener(CommutatorTable::SHOW_DETAIL.':Direct')]
    public function onCommutatorTableShowDetailDirect(): void
    {
        $this->camera = null;
    }

}