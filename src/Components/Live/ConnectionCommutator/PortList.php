<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
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
class PortList
{
    use DefaultActionTrait;
    use ComponentActiveInactive;
    use ComponentToolsTrait;

    const DEACTIVATE_PORT = 'port_list:deactivate:port';
    const ACTIVATE_PORT = 'port_list:activate:port';
    const SELECTED_PORT = 'port_list:selected:port';

    #[LiveProp]
    public ?array $ports = null;

    #[LiveProp]
    public ?int $selected = null;

    #[LiveProp]
    public ?int $editing = null;

    #[LiveProp(writable: true )]
    public ?string $speed = null;

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
        return static::DEACTIVATE_PORT.':'.$this->connection->name;
    }

    /**
     * Get deactivate event name
     * @return string
     */
    private function getActivateEventName(): string
    {
        return static::ACTIVATE_PORT.':'.$this->connection->name;
    }

    #[LiveAction]
    public function select(#[LiveArg] ?int $portId): void
    {
        $this->selected = $portId;
        $this->emit(static::SELECTED_PORT.':'.$this->connection->name, [
            'port' => $portId,
        ]);
    }

    #[LiveAction]
    public function activateEditing(#[LiveArg] ?int $portId, #[LiveArg] ?int $speed): void
    {
        $this->editing = $portId;
        $this->speed = $speed;
    }

    /**
     * Recollect ports info for commutator
     * @param Commutator $commutator
     * @return array
     */
    private function portsInfo(Commutator $commutator): array
    {
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
        $data['speed'] = $port->getSpeed();
        $data['id'] = $port->getId();
        $data['isSelectable'] = true;

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

    #[LiveListener(CommutatorTable::SHOW_DETAIL.':Direct')]
    public function onCommutatorShowDetailDirect(#[LiveArg] Commutator $entity): void
    {

        $this->ports = $this->portsInfo($entity);
        $this->selected = null;
        //$this->select(null);//ya NewCamera escucha CommutatorTable::SHOW_DETAIL
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    #[LiveListener(CommutatorTable::CHANGE_TABLE.':Direct')]
    public function onChangeTableDirect(): void
    {
        $this->ports = null;
    }

    #[LiveListener(CommutatorDetail::DEACTIVATE_SWITCH.':Direct')]
    public function onDeactivateSwitchDirect(#[LiveArg] Commutator $entity): void
    {
        $this->select(null);
        foreach($this->ports as $key=>$value){
            $this->deactivatePort($key);
        }
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
            $this->select(null);
        }
    }

    #[LiveAction]
    public function save(PortRepository $portRepository, #[LiveArg] Port $port): void
    {
        if(!$this->speed){
            $this->speed = 1;
        }
        $this->ports[$port->getId()]['speed'] = $this->speed;
        $port->configure($this->speed);
        $portRepository->save($port, true);
        $this->editing = null;
    }

}