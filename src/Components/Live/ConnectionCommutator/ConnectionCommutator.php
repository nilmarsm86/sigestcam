<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/conecction_commutator.html.twig')]
class ConnectionCommutator
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $isShowDetail = false;

    #[LiveProp]
    public ?array $commutator = null;

    #[LiveProp(writable: true)]
    public ?ConnectionType $connection = null;

    #[LiveListener(CommutatorDetail::DEACTIVATE_SWITCH)]
    public function onDeactivateSwitch(#[LiveArg] Commutator $commutator): void
    {
        $this->isShowDetail = false;
        //$this->save($commutator);
    }

    /*#[LiveListener(NewCommutatorForm::FORM_SUCCESS)]
    public function onFormSuccess(#[LiveArg] Commutator $commutator): void
    {
        $this->isShowDetail = false;
    }*/

    /**
     * Update table from filter, amount or page
     * @return void
     */
    #[LiveListener(CommutatorTable::CHANGE_TABLE)]
    public function onChangeTable(): void
    {
        $this->isShowDetail = false;
    }

    private function details(Commutator $commutator): void
    {
        $this->commutator['id'] = $commutator->getId();
        $this->commutator['ip'] = $commutator->getIp();
        $this->commutator['gateway'] = $commutator->getGateway();
        $this->commutator['inventary'] = $commutator->getInventory();
        $this->commutator['physical_address'] = $commutator->getPhysicalAddress();
        $this->commutator['brand'] = $commutator->getBrand();
        $this->commutator['model'] = $commutator->getModel();
        $this->commutator['contic'] = $commutator->getContic();
        $this->commutator['serial'] = $commutator->getPhysicalSerial();
        $this->commutator['province'] = (string) $commutator->getMunicipality()->getProvince();
        $this->commutator['municipality'] = (string) $commutator->getMunicipality();
        $this->commutator['ports'] = $this->portsInfo($commutator);
        $this->commutator['state'] = $commutator->isActive();
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

    #[LiveListener(CommutatorTable::SHOW_DETAIL)]
    public function onShowDetail(#[LiveArg] Commutator $commutator): void
    {
        if(isset($this->commutator['id'])){
            if($this->commutator['id'] !== $commutator->getId()){
                $this->details($commutator);
                $this->isShowDetail = true;
            }
        }else{
            $this->details($commutator);
            $this->isShowDetail = true;
        }
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

}