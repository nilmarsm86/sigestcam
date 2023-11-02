<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Card;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Msam;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/detail.html.twig')]
class ConnectionMsamDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    const DEACTIVATE = self::class.'_deactivate';
    const DISCONNECT = self::class.'_disconnect';
    const CONNECT = self::class.'_connect';
    const ACTIVATE = self::class.'_activate';

    #[LiveProp()]
    public ?array $msam = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Msam::class;
    }

    /**
     * TODO: cuando el componente ya esta montado pero se llama como si fuera la primera vez
     * @return void
     */
    public function __invoke(): void
    {
        $this->msam = null;
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
     * Get activate event name
     * @return string
     */
    protected function getActivateEventName(): string
    {
        return static::ACTIVATE.'_'.$this->connection->name;
    }

    /**
     * Get connect event name
     * @return string
     */
    protected function getConnectEventName(): string
    {
        return static::CONNECT.'_'.$this->connection->name;
    }

    /**
     * Get disconnect event name
     * @return string
     */
    protected function getDisconnectEventName(): string
    {
        return static::DISCONNECT.'_'.$this->connection->name;
    }

    /**
     * @param Msam $entity
     * @return void
     */
    protected function onConnectionMsamTableDetail(#[LiveArg] Msam $entity): void
    {
        if(isset($this->msam['id'])){
            if($this->msam['id'] !== $entity->getId()){
                $this->msam = $this->details($entity);
                $this->active = $this->msam['state'];
            }
        }else{
            $this->msam = $this->details($entity);
            $this->active = $this->msam['state'];
        }
    }

    #[LiveListener(ConnectionMsamTable::DETAIL.'_Full')]
    public function onConnectionMsamTableDetailFull(#[LiveArg] Msam $entity): void
    {
        $this->onConnectionMsamTableDetail($entity);
    }

    /**
     * @param Msam $msam
     * @return array
     */
    protected function details(Msam $msam): array
    {
        $switch = [];
        $switch['id'] = $msam->getId();
        $switch['inventary'] = $msam->getInventory();
        $switch['physical_address'] = $msam->getPhysicalAddress();
        $switch['brand'] = $msam->getBrand();
        $switch['model'] = $msam->getModel();
        $switch['contic'] = $msam->getContic();
        $switch['serial'] = $msam->getPhysicalSerial();
        $switch['province'] = (string) $msam->getMunicipality()->getProvince();
        $switch['municipality'] = (string) $msam->getMunicipality();
        $switch['cards'] = $this->cardsInfo($msam);
        $switch['state'] = $msam->isActive();

        return $switch;
    }

    /**
     * Recollect ports info for commutator
     * @param Commutator $commutator
     * @return array
     */
    protected function cardsInfo(Msam $msam): array
    {
        $cards = [];
        foreach($msam->getCards() as $card){
            $cards[$card->getId()] = $this->cardData($card);
        }

        return $cards;
    }

    /**
     * @param Card $card
     * @return array
     */
    protected function cardData(Card $card): array
    {
        $data = [];
        $data['id'] = $card->getId();
        $data['slot'] = $card->getSlot();
        $data['name'] = $card->getName();
        $data['ports'] = $this->portsInfo($card);

        return $data;
    }

    /**
     * Recollect ports info for commutator
     * @param Commutator $commutator
     * @return array
     */
    protected function portsInfo(Card $card): array
    {
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
        $data['state'] = $port->isActive();
        $data['equipment'] = $port?->getEquipment()?->getShortName();
        $data['speed'] = $port->getSpeed();
        $data['id'] = $port->getId();

        if (is_null($port->getConnectionType())) {
            $data['connection'] = 'bg-gradient-danger';
        } else {
            if ($port->getConnectionType() === $this->connection) {
                $data['connection'] = 'bg-gradient-success';
            } else {
                $data['connection'] = 'bg-gradient-primary';
            }
        }

        return $data;
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    protected function onConnectionMsamTableChange(): void
    {
        $this->msam = null;
    }

    #[LiveListener(ConnectionMsamTable::CHANGE.'_Full')]
    public function onConnectionMsamTableChangeFull(): void
    {
        $this->onConnectionMsamTableChange();
    }

    #[LiveAction]
    public function connect(#[LiveArg] Msam $msam): void
    {
        $msam->connect($this->port);

        $this->entityManager->persist($msam);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function disconnect(#[LiveArg] Msam $msam): void
    {
        $port = $msam->getPort();
        $msam->disconnect();

        $this->entityManager->persist($msam);
        $this->entityManager->flush();
        $this->active = false;

        $this->emit($this->getDisconnectEventName(), [
            'port' => $port->getId(),
        ]);
    }

    #[LiveAction]
    public function preactivate(#[LiveArg] int $entityId, #[LiveArg] string $elementId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        if(is_null($entity->getPort())){
            //TODO: porque dispara el evento 2 veces?
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

        $this->modem = null;

        $this->emit($this->getDeactivateEventName(), [
            'entity' => $entity->getId(),
        ]);
    }

}