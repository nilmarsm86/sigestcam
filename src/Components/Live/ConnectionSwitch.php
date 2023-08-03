<?php

namespace App\Components\Live;


use App\DTO\Paginator;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/conecction_switch.html.twig')]
class ConnectionSwitch
{
    use DefaultActionTrait;

    #[LiveProp(useSerializerForHydration: true)]
    public Paginator $paginator;

    #[LiveProp(writable: true)]
    public string $filter = '';

    #[LiveProp]
    public ?array $commutator = null;

    #[LiveProp(writable: true)]
    public bool $isShowDetail = false;

    #[LiveProp(writable: true)]
    public ?array $data = null;

    #[LiveProp(writable: true)]
    public ?int $amount = null;

    #[LiveProp(writable: true)]
    public ?int $page = null;

    #[LiveProp(writable: true)]
    public ?int $fake = null;

    #[LiveProp(writable: true)]
    public ?string $url = null;

    #[LiveProp(writable: true)]
    public ?ConnectionType $connection = null;

    #[LiveAction]
    public function save(#[LiveArg] Commutator $commutator): void
    {
        $this->configPaginator();
        $this->details($commutator);
        $this->portInfo($commutator);
        $this->isShowDetail = true;
    }

    private function configPaginator(): void
    {
        $this->paginator->setFake($this->fake);
        $this->paginator->setAmount($this->amount);
        $this->paginator->setPage($this->page);
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
        $this->commutator['ports'] = $this->portInfo($commutator);
        $this->commutator['state'] = $commutator->isActive();
    }

    private function portInfo(Commutator $commutator): array
    {
        $this->commutator['ports'] = [];

        foreach($commutator->getPorts() as $port){
            $this->commutator['ports'][$port->getId()]['number'] = $port->getNumber();
            $this->commutator['ports'][$port->getId()]['state'] = $port->isActive();
            $this->commutator['ports'][$port->getId()]['equipment'] = (string) $port->getEquipment();
            $this->commutator['ports'][$port->getId()]['speed'] = $port->getSpeed();
            $this->commutator['ports'][$port->getId()]['id'] = $port->getId();

            if(is_null($port->getConnectionType())){
                $this->commutator['ports'][$port->getId()]['connection'] = 'bg-gradient-danger';
            }else{
                if($port->getConnectionType() === $this->connection){
                    $this->commutator['ports'][$port->getId()]['connection'] = 'bg-gradient-success';
                }else{
                    $this->commutator['ports'][$port->getId()]['connection'] = 'bg-gradient-primary';
                }
            }
        }

        return $this->commutator['ports'];
    }

}