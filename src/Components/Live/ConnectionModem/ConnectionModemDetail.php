<?php

namespace App\Components\Live\ConnectionModem;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_modem/detail.html.twig')]
class ConnectionModemDetail
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentActiveInactive;

    const DEACTIVATE = self::class.'_deactivate';
    const DISCONNECT = self::class.'_disconnect';
    const CONNECT = self::class.'_connect';
    const ACTIVATE = self::class.'_activate';

    #[LiveProp]
    public ?array $modem = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
        $this->entity = Modem::class;
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->modem = null;
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

    public function onConnectionModemTableDetail(Modem $entity): void
    {
        if(isset($this->modem['id'])){
            if($this->modem['id'] !== $entity->getId()){
                $this->modem = $this->details($entity);
                $this->active = $this->modem['state'];
            }
        }else{
            $this->modem = $this->details($entity);
            $this->active = $this->modem['state'];
        }
    }

    #[LiveListener(ConnectionModemTable::DETAIL.'_Simple')]
    public function onConnectionModemTableDetailSimple(#[LiveArg] Modem $entity): void
    {
        $this->onConnectionModemTableDetail($entity);
    }

    #[LiveListener(ConnectionModemTable::DETAIL.'_SlaveModem')]
    public function onConnectionModemTableDetailSlaveModem(#[LiveArg] Modem $entity): void
    {
        $this->onConnectionModemTableDetail($entity);
    }

    protected function details(Modem $modem): array
    {
        $mod = [];
        $mod['id'] = $modem->getId();
        $mod['ip'] = $modem->getIp();
        $mod['inventory'] = $modem->getInventory();
        $mod['physical_address'] = $modem->getPhysicalAddress();
        $mod['brand'] = $modem->getBrand();
        $mod['model'] = $modem->getModel();
        $mod['contic'] = $modem->getContic();
        $mod['serial'] = $modem->getPhysicalSerial();
        $mod['province'] = (string) $modem->getMunicipality()->getProvince();
        $mod['municipality'] = (string) $modem->getMunicipality();
        $mod['state'] = $modem->isActive();
        $mod['disconnected'] = $modem->isDisconnected();
        $mod['master_modem'] = (string) $modem->getMasterModem();
//        $mod['electronicSerial'] = (string) $modem->getElectronicSerial();
        $mod['commutator'] = (string) $modem->getPort()?->getCommutator();
        $mod['port'] = $modem->getPort()?->getNumber();

        return $mod;
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    #[LiveListener(ConnectionModemTable::CHANGE.'_Simple')]
    public function onConnectionModemTableChangeSimple(): void
    {
        $this->modem = null;
    }

    #[LiveAction]
    public function connect(#[LiveArg] Modem $modem): void
    {
        $modem->connect($this->port);

        $this->entityManager->persist($modem);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function disconnect(#[LiveArg] Modem $modem): void
    {
        $port = $modem->getPort();
        $modem->disconnect();
        $this->entityManager->persist($modem);
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