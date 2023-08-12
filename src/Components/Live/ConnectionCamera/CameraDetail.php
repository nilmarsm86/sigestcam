<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionCommutator\CommutatorTable;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/camera_detail.html.twig')]
class CameraDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    const DEACTIVATE_SWITCH = 'camera_detail:deactivate:switch';

    #[LiveProp]
    public ?array $camera = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    //#[LiveProp(updateFromParent: true)]
    //public ?Commutator $commutator = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;


    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Camera::class;
    }

    /*public function mount(Port $port): void
    {
        //$this->active = $camera['state'];
        //$this->camera = $camera;

    }*/

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->camera = null;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    private function getDeactivateEventName(): string
    {
        return static::DEACTIVATE_SWITCH.':'.$this->connection->name;
    }

    #[LiveListener(CameraTable::SHOW_DETAIL.':Direct')]
    public function onShowDetailDirect(#[LiveArg] Camera $entity): void
    {
        if(isset($this->camera['id'])){
            if($this->camera['id'] !== $entity->getId()){
                $this->camera = $this->details($entity);
                $this->active = $this->camera['state'];
            }
        }else{
            $this->camera = $this->details($entity);
            $this->active = $this->camera['state'];
        }
    }

    private function details(Camera $camera): array
    {
        $switch = [];
        $switch['id'] = $camera->getId();
        $switch['ip'] = $camera->getIp();
        $switch['inventary'] = $camera->getInventory();
        $switch['physical_address'] = $camera->getPhysicalAddress();
        $switch['brand'] = $camera->getBrand();
        $switch['model'] = $camera->getModel();
        $switch['contic'] = $camera->getContic();
        $switch['serial'] = $camera->getPhysicalSerial();
        $switch['province'] = (string) $camera->getMunicipality()->getProvince();
        $switch['municipality'] = (string) $camera->getMunicipality();
        $switch['state'] = $camera->isActive();

        return $switch;
    }

    /*#[LiveListener(CommutatorDetail::DEACTIVATE_SWITCH.':Direct')]
    public function onDeactivateSwitchDirect(#[LiveArg] Commutator $entity): void
    {
        $this->camera = null;
    }*/

    /*#[LiveListener(PortList::DEACTIVATE_PORT.':Direct')]
    public function onPortListDeactivatePortDirect(#[LiveArg] Port $entity): void
    {
        $this->camera = null;
    }*/

    /**
     * Update table from filter, amount or page
     * @return void
     */
    #[LiveListener(CameraTable::CHANGE_TABLE.':Direct')]
    public function onCameraTableChangeTableDirect(): void
    {
        $this->camera = null;
    }

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void

    #[LiveListener(CommutatorTable::CHANGE_TABLE.':Direct')]
    public function onCommutatorTableChangeTableDirect(): void
    {
        $this->camera = null;
    }*/

    /**
     * Update table from filter, amount or page just in direct connections
     * @return void

    #[LiveListener(CommutatorTable::SHOW_DETAIL.':Direct')]
    public function onCommutatorTableShowDetailDirect(): void
    {
        $this->camera = null;
    }*/



}