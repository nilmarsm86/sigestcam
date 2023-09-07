<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\ConnectionCamera\ConnectionCameraDetail;
use App\Components\Live\ConnectionCamera\ConnectionCameraNew;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\PortType;
use App\Entity\Port;
use App\Repository\PortRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/port_list.html.twig')]
class ConnectionCommutatorPortList
{
    use DefaultActionTrait;
    use ComponentActiveInactive;
    use ComponentToolsTrait;

    const DEACTIVATE = self::class.'_deactivate';
    const ACTIVATE = self::class.'_activate';
    const SELECTED = self::class.'_selected';

    #[LiveProp]
    public ?array $ports = null;

    #[LiveProp]
    public ?int $selected = null;

    #[LiveProp]
    public ?int $editingSpeed = null;

    #[LiveProp]
    public ?int $editingType = null;

    #[LiveProp]
    public ?array $forSelect = null;

    #[LiveProp(writable: true )]
    public ?string $speed = null;

    #[LiveProp(writable: true )]
    public ?string $type = null;

    #[LiveProp]
    public ?array $commutator = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Port::class;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    private function getDeactivateEventName(): string
    {
        return static::DEACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    private function getActivateEventName(): string
    {
        return static::ACTIVATE.'_'.$this->connection->name;
    }

    #[LiveAction]
    public function select(#[LiveArg] ?int $portId): void
    {
        $this->selected = $portId;
        foreach($this->ports as $port){
            if($port['id'] === $portId){
                $port['isSelectable'] = false;
            }else{
                $port['isSelectable'] = true;
            }
        }

        $this->emit(static::SELECTED.'_'.$this->connection->name, [
            'port' => $portId,
        ]);
    }

    #[LiveAction]
    public function activateEditingSpeed(#[LiveArg] ?int $portId, #[LiveArg] ?int $speed): void
    {
        $this->editingSpeed = $portId;
        $this->speed = $speed;
    }

    #[LiveAction]
    public function activateEditingType(#[LiveArg] ?int $portId, #[LiveArg] ?int $type): void
    {
        $this->editingType = $portId;
        $this->type = $type;
    }

    /**
     * Recollect ports info for commutator
     * @param Commutator $commutator
     * @return array
     */
    private function portsInfo(Commutator $commutator): array
    {
        $this->forSelect = PortType::forSelect();
        $ports = [];
        foreach($commutator->getPorts() as $port){
            $ports[$port->getId()] = $this->portData($port);
        }

        return $ports;
    }

    /**
     * Recollect port info
     * @param Port $port
     * @return array
     */
    private function portData(Port $port): array
    {
        $data = [];
        $data['number'] = $port->getNumber();
        $data['state'] = (bool) $port->isActive();
        $data['equipment'] = $port?->getEquipment()?->getShortName();
        $data['equipment_state'] = ($port?->getEquipment()?->isActive()) ? '' : 'text-bg-danger';
        $data['speed'] = $port->getSpeed();
        $data['id'] = $port->getId();
        $data['isSelectable'] = true;
        $data['type_value'] = $port->getTypeValue();
        $data['type_label'] = $port->getTypeLabel();

        if (is_null($port->getConnectionType())) {
            $data['connection'] = 'bg-gradient-danger';
        } else {
            if ($port->getConnectionType() === $this->connection) {
                $data['connection'] = 'bg-gradient-success';
            } else {
                $data['connection'] = 'bg-gradient-primary';
                $data['isSelectable'] = false;
            }
        }

        if($port->getId() === $this->selected){
            $data['isSelectable'] = false;
        }

        return $data;
    }

    private function onConnectionCommutatorTableDetail(Commutator $entity): void
    {
        $this->ports = $this->portsInfo($entity);
        $this->selected = null;
        $this->editingSpeed = null;
        $this->editingType = null;
        //$this->select(null);//ya ConnectionCameraNew escucha ConnectionCameraTable::SHOW_DETAIL
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Direct')]
    public function onConnectionCommutatorTableDetailDirect(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Simple')]
    public function onConnectionCommutatorTableDetailSimple(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveSwitch')]
    public function onConnectionCommutatorTableDetailSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_SlaveModem')]
    public function onConnectionCommutatorTableDetailSlaveModem(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    #[LiveListener(ConnectionCommutatorTable::DETAIL.'_Full')]
    public function onConnectionCommutatorTableDetailFull(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorTableDetail($entity);
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    public function onConnectionCommutatorTableChange(): void
    {
        $this->ports = null;
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

    private function onConnectionCommutatorDetailDeactivate(Commutator $entity): void
    {
        $this->select(null);
        foreach($this->ports as $key=>$value){
            $this->deactivatePort($key);
        }
    }

    #[LiveListener(ConnectionCommutatorDetail::DEACTIVATE.'_Direct')]
    public function onConnectionCommutatorDetailDeactivateDirect(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorDetailDeactivate($entity);
    }

    #[LiveListener(ConnectionCommutatorDetail::DEACTIVATE.'_Simple')]
    public function onConnectionCommutatorDetailDeactivateSimple(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorDetailDeactivate($entity);
    }

    #[LiveListener(ConnectionCommutatorDetail::DEACTIVATE.'_SlaveSwitch')]
    public function onConnectionCommutatorDetailDeactivateSlaveSwitch(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorDetailDeactivate($entity);
    }

    #[LiveListener(ConnectionCommutatorDetail::DEACTIVATE.'_SlaveModem')]
    public function onConnectionCommutatorDetailDeactivateSlaveModem(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorDetailDeactivate($entity);
    }

    #[LiveListener(ConnectionCommutatorDetail::DEACTIVATE.'_Full')]
    public function onConnectionCommutatorDetailDeactivateFull(#[LiveArg] Commutator $entity): void
    {
        $this->onConnectionCommutatorDetailDeactivate($entity);
    }

    #[LiveAction]
    public function activatePort(#[LiveArg] ?int $portId): void
    {
        $this->ports[$portId]['state'] = true;
        $this->activate($portId);
    }

    #[LiveAction]
    public function deactivatePort(#[LiveArg] ?int $portId): void
    {
        $this->ports[$portId]['state'] = false;
        $this->deactivate($portId);
        if($this->selected === $portId){
            $this->select($portId);
//            $this->selected = null;
        }
    }

    #[LiveAction]
    public function saveSpeed(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        if(!$this->speed){
            $this->speed = 1;
        }
        $this->ports[$port->getId()]['speed'] = $this->speed;
        $port->configure($this->speed);
        $portRepository->save($port, true);
        $this->editingSpeed = null;
    }

    #[LiveAction]
    public function saveType(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        $this->ports[$port->getId()]['type_value'] = $this->type;
        $this->ports[$port->getId()]['type_label'] = PortType::getLabelFrom($this->type);
        $port->configure($this->ports[$port->getId()]['speed'], PortType::from($this->type));
        $portRepository->save($port, true);
        $this->editingType = null;
    }

    #[LiveListener(ConnectionCameraNew::FORM_SUCCESS.'_Direct')]
    public function onConnectionCameraNewFormSuccessDirect(#[LiveArg] Camera $camera): void
    {
        $this->ports[$camera->getPort()->getId()]['equipment'] = $camera->getShortName();
        $this->ports[$camera->getPort()->getId()]['isSelectable'] = false;
    }

    #[LiveListener(ConnectionCameraDetail::DISCONNECT.'_Direct')]
    public function onConnectionCameraDetailDisconnectDirect(#[LiveArg] Port $port): void
    {
        $this->ports[$port->getId()]['equipment'] = null;
        $this->ports[$port->getId()]['connection'] = 'bg-gradient-danger';
    }

}