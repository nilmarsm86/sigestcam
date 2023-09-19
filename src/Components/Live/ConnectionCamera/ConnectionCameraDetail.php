<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
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
    const ACTIVATE = self::class.'_activate';
    const DISCONNECT = self::class.'_disconnect';
    const CONNECT = self::class.'_connect';

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
    protected function getDeactivateEventName(): string
    {
        return static::DEACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    protected function getActivateEventName(): string
    {
        return static::ACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get disconnect event name
     * @return string
     */
    private function getDisconnectEventName(): string
    {
        return static::DISCONNECT.'_'.$this->connection->name;
    }

    /**
     * Get connect event name
     * @return string
     */
    private function getConnectEventName(): string
    {
        return static::CONNECT.'_'.$this->connection->name;
    }

    private function onConnectionCameraTableDetail(Camera $entity): void
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
        $cam['disconnected'] = $camera->isDisconnected();
        $cam['user'] = $camera->getUser();
        $cam['password'] = $camera->getPassword();
        $cam['modem'] = (string) $camera->getModem();
        $cam['electronicSerial'] = (string) $camera->getElectronicSerial();
        $cam['commutator'] = (string) $camera->getPort()?->getCommutator();
        $cam['port'] = $camera->getPort()?->getNumber();

        return $cam;
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
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

    #[LiveAction]
    public function connect(#[LiveArg] Camera $camera): void
    {
        if($this->port->hasConnectedModem()){
            $camera->connect($this->port->getEquipment());
        }else{
            $camera->connect($this->port);
        }

        $this->entityManager->persist($camera);
        $this->entityManager->flush();

        if($this->port->hasConnectedModem()){
            $this->camera = null;
            $this->emit($this->getConnectEventName(), [
                'modem' => $this->port->getEquipment()->getId(),
            ]);
        }
    }

    #[LiveAction]
    public function disconnect(#[LiveArg] Camera $camera): void
    {
        $camera->disconnect();

        $this->entityManager->persist($camera);
        $this->entityManager->flush();
        $this->active = false;

        $port = $camera->getPort();
        if(is_null($port)){
            $port = $this->port;
        }

        if($port->hasConnectedModem()){
            $this->camera = null;
            $this->emit($this->getDisconnectEventName(), [
                'modem' => $port->getEquipment()->getId(),
            ]);
        }else{
            $this->emit($this->getDisconnectEventName(), [
                'port' => $port->getId(),
            ]);
        }
    }

    #[LiveAction]
    public function preactivate(#[LiveArg] int $entityId, #[LiveArg] string $elementId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        if($entity->notModemNotPort()){
            $this->dispatchBrowserEvent($this->getActivateEventName(), [
                'elementId' => $elementId
            ]);
        }else{
            $entity->activate();

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->active = true;

            $this->emit($this->getActivateEventName(), [
                'entity' => $entity->getId(),
            ]);
        }
    }

    #[LiveAction]
    public function predeactivate(#[LiveArg] int $entityId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        $entity->deactivate();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->active = false;

        $this->camera = null;

        $this->emit($this->getDeactivateEventName(), [
            'entity' => $entity->getId(),
        ]);
    }

}