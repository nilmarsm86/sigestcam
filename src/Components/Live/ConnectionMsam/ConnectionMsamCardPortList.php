<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Card;
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

#[AsLiveComponent(template: 'components/live/connection_msam/card_port_list.html.twig')]
class ConnectionMsamCardPortList
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
    public ?Card $card = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Port::class;
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
     * Get deactivate event name
     * @return string
     */
    protected function getSelectedEventName(): string
    {
        return static::SELECTED.'_'.$this->connection->name;
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

        $this->emit($this->getSelectedEventName(), [
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
    protected function portsInfo(Card $card): array
    {
        $this->forSelect = PortType::forSelect();
        $ports = [];
        foreach($card->getPorts() as $port){
            $ports[$port->getId()] = $this->portData($port);
        }

        return $ports;
    }

    /**
     * Recollect port info
     * @param Port $port
     * @return array
     */
    protected function portData(Port $port): array
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

    #[LiveListener(ConnectionMsamCardTable::DETAIL.'_Full')]
    public function onConnectionMsamCardTableDetailFull(#[LiveArg] Card $entity): void
    {
        $this->ports = $this->portsInfo($entity);
        $this->selected = null;
        $this->editingSpeed = null;
        $this->editingType = null;
    }

    #[LiveListener(ConnectionMsamCardTable::CHANGE.'_Full')]
    public function onConnectionMsamCardTableChangeFull(): void
    {
        $this->ports = null;
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

    /*//TODO: porque solamente esta en la conexion directa
    #[LiveListener(ConnectionMsamCardNew::FORM_SUCCESS.'_Full')]
    public function onConnectionCameraNewFormSuccessDirect(#[LiveArg] Card $card): void
    {
        $this->ports[$camera->getPort()->getId()]['equipment'] = $camera->getShortName();
        $this->ports[$camera->getPort()->getId()]['isSelectable'] = false;
    }*/

}