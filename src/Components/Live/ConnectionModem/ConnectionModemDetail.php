<?php

namespace App\Components\Live\ConnectionModem;

use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Camera;
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
    const ACTIVATE = self::class.'_activate';

    #[LiveProp]
    public ?array $modem = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
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

    #[LiveListener(ConnectionModemTable::DETAIL.'_Simple')]
    public function onConnectionModemTableDetailSimple(#[LiveArg] Modem $entity): void
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

    private function details(Modem $modem): array
    {
        $cam = [];
        $cam['id'] = $modem->getId();
        $cam['ip'] = $modem->getIp();
        $cam['inventory'] = $modem->getInventory();
        $cam['physical_address'] = $modem->getPhysicalAddress();
        $cam['brand'] = $modem->getBrand();
        $cam['model'] = $modem->getModel();
        $cam['contic'] = $modem->getContic();
        $cam['serial'] = $modem->getPhysicalSerial();
        $cam['province'] = (string) $modem->getMunicipality()->getProvince();
        $cam['municipality'] = (string) $modem->getMunicipality();
        $cam['state'] = $modem->isActive();
        $cam['disconnected'] = $modem->isDisconnected();
        $cam['master_modem'] = (string) $modem->getMasterModem();
//        $cam['electronicSerial'] = (string) $modem->getElectronicSerial();
        $cam['commutator'] = (string) $modem->getPort()?->getCommutator();
        $cam['port'] = $modem->getPort()?->getNumber();

        return $cam;
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

        $this->emit(static::DISCONNECT.'_'.$this->connection->name, [
            'port' => $port->getId(),
        ]);
    }

    #[LiveAction]
    public function preactivate(#[LiveArg] int $entityId, #[LiveArg] string $elementId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        if(is_null($entity->getPort())){
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

}