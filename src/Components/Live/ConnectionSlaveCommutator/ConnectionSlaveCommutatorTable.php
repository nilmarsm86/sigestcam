<?php

namespace App\Components\Live\ConnectionSlaveCommutator;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_commutator/table.html.twig')]
class ConnectionSlaveCommutatorTable extends ConnectionCommutatorTable
{
    use ComponentToolsTrait;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp(updateFromParent: true)]
    public ?Port $masterPort = null;

    #[LiveProp(updateFromParent: true)]
    public bool $inactive = false;

    /**
     * cuando se monta por primera vez el componete
     * @param ConnectionType|null $connection
     * @param Port|null $masterPort
     * @param bool $inactive
     * @return void
     */
    public function mount(ConnectionType $connection = null, Port $masterPort = null, bool $inactive = false): void
    {
        $this->connection = $connection;
        $this->masterPort = $masterPort;
        $this->inactive = $inactive;
        $this->filterAndReload();
    }

    /**
     * cuando el componente ya esta montado pero se llama como si fuera la primera vez
     * @return void
     */
    public function __invoke(): void
    {
        $this->page = 1;
        $this->filterAndReload();
        if($this->inactive){
            $this->emit($this->getChangeTableEventName());
        }
    }

    /**
     * @return void
     */
    protected function filterAndReload(): void
    {
        $this->entityId = null;
        $this->filter = ($this->masterPort?->hasConnectedCommutator()) ? $this->masterPort->getEquipment()->getIp() : '';
        $this->reload();
    }

    /**
     * @return void
     */
    protected function reload()
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }

        //TODO: cambiar la forma en la que se buscan los datos
        if($this->masterPort->hasConnectedCommutator()){
            $data = $this->commutatorRepository->findByPort($this->masterPort, $this->filter, $this->amount, $this->page);
        }else{
            if($this->inactive){
                $data = $this->commutatorRepository->findInactiveWithoutPort($this->filter, $this->amount, $this->page);
            }else{
                $data = $this->commutatorRepository->findActiveAndSlave($this->filter, $this->amount, $this->page);
            }
        }
        $this->reloadData($data);
    }

    #[LiveListener(ConnectionSlaveCommutatorNew::FORM_SUCCESS.'_SlaveSwitch')]
    public function onConnectionSlaveCommutatorNewFormSuccessSlaveSwitch(#[LiveArg] Commutator $commutator): void
    {
        $this->onConnectionCommutatorNewFormSuccess($commutator);
    }

}