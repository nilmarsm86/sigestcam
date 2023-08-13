<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/detail.html.twig')]
class ConnectionCameraDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    const DEACTIVATE = self::class.'_deactivate';

    #[LiveProp]
    public ?array $camera = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Camera::class;
    }

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
        return static::DEACTIVATE.'_'.$this->connection->name;
    }

    #[LiveListener(ConnectionCameraTable::DETAIL.'_Direct')]
    public function onConnectionCameraTableDetailDirect(#[LiveArg] Camera $entity): void
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
        $cam = [];
        $cam['id'] = $camera->getId();
        $cam['ip'] = $camera->getIp();
        $cam['inventary'] = $camera->getInventory();
        $cam['physical_address'] = $camera->getPhysicalAddress();
        $cam['brand'] = $camera->getBrand();
        $cam['model'] = $camera->getModel();
        $cam['contic'] = $camera->getContic();
        $cam['serial'] = $camera->getPhysicalSerial();
        $cam['province'] = (string) $camera->getMunicipality()->getProvince();
        $cam['municipality'] = (string) $camera->getMunicipality();
        $cam['state'] = $camera->isActive();

        return $cam;
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    #[LiveListener(ConnectionCameraTable::CHANGE.'_Direct')]
    public function onConnectionCameraTableChangeDirect(): void
    {
        $this->camera = null;
    }

}