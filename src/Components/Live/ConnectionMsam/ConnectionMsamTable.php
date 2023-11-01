<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Msam;
use App\Entity\Port;
use App\Repository\CardRepository;
use App\Repository\MsamRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/table.html.twig')]
class ConnectionMsamTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp(updateFromParent: true)]
    public ?Port $port = null;

    #[LiveProp(updateFromParent: true)]
    public ?Msam $msam = null;

    public function __construct(protected readonly MsamRepository $msamRepository, protected readonly CardRepository $cardRepository)
    {
    }

    //cuando se monta por primera vez el componete
    public function mount(ConnectionType $connection, Port $port): void
    {
        $this->connection = $connection;
        $this->port = $port;
        $this->filterAndReload();
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->page = 1;
        $this->filterAndReload();
    }

    protected function filterAndReload(): void
    {
        $this->entityId = null;
        $this->filter = ($this->port->hasConnectedMsam()) ? $this->port->getEquipment()->getIp() : '';
        $this->reload();
    }

    protected function reload(): void
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }

        //cambiar la forma en la que se buscan los datos
        if($this->port->hasConnectedMsam()){
            $data = $this->msamRepository->findMsamByPort($this->port, $this->filter, $this->amount, $this->page);
        }else{
            $data = $this->msamRepository->findInactiveMsamWithoutPort($this->filter, $this->amount, $this->page);
        }
        $this->reloadData($data);
        $this->setCardsAmount();
    }

    //mejorar
    protected function setCardsAmount()
    {
        for($i=0;$i<count($this->data);$i++){
            $this->data[$i]['cards'] = $this->cardRepository->findAmountCardsByMsamId($this->data[$i]['id']);
        }
    }

    /**
     * When save new commutator table filer by it
     * @param Commutator $commutator
     * @return void
     */
    protected function onConnectionMsamNewFormSuccess(Msam $msam): void
    {
        $this->filter = $msam->getPhysicalSerial();//filtrar por numero de serie
        $this->changeFilter();
    }

    #[LiveListener(ConnectionMsamNew::FORM_SUCCESS.'_Full')]
    public function onConnectionMsamNewFormSuccessFull(#[LiveArg] Msam $msam): void
    {
        $this->onConnectionMsamNewFormSuccess($msam);
    }

    /**
     * Get change table event name
     * @return string
     */
    protected function getChangeTableEventName(): string
    {
        return static::CHANGE.'_'.$this->connection->name;
    }

    /**
     * Get show detail event name
     * @return string
     */
    protected function getShowDetailEventName(): string
    {
        return static::DETAIL.'_'.$this->connection->name;
    }

    public function onConnectionMsamDetailEditInlineSave(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionDetailEditInline::SAVE_MSAM.'_Full')]
    public function onConnectionMsamDetailEditInlineFull(): void
    {
        $this->onConnectionMsamDetailEditInlineSave();
    }

    public function onConnectionMsamDetailActivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionMsamDetail::ACTIVATE.'_Full')]
    public function onConnectionMsamDetailActivateSimple(): void
    {
        $this->onConnectionMsamDetailActivate();
    }

    public function onConnectionMsamDetailDeactivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionMsamDetail::DEACTIVATE.'_Full')]
    public function onConnectionMsamDetailDeactivateSimple(): void
    {
        $this->onConnectionMsamDetailDeactivate();
    }

}