<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionCamera\ConnectionCameraDetail;
use App\Components\Live\ConnectionCamera\ConnectionCameraNew;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorDetail;
use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionDetailEditInline;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Entity\Card;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Enums\PortType;
use App\Entity\Msam;
use App\Entity\Port;
use App\Repository\CardRepository;
use App\Repository\MsamRepository;
use App\Repository\PortRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/card_table.html.twig')]
class ConnectionMsamCardTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE = self::class.'_change';
    const DETAIL = self::class.'_detail';

    #[LiveProp]
    public ?ConnectionType $connection = null;

    #[LiveProp]
    public ?int $slot = null;

    #[LiveProp]
    public ?Msam $msam = null;

    #[LiveProp]
    public ?Card $card = null;

    public function __construct(protected readonly CardRepository $cardRepository, protected readonly PortRepository $portRepository)
    {}

    //cuando se monta por primera vez el componete
    public function mount(ConnectionType $connection): void
    {
        $this->connection = $connection;
        $this->filterAndReload();
    }

    //cuando el componente ya esta montado pero se llama como si fuera la primera vez
    public function __invoke(): void
    {
        $this->page = 1;
        $this->amount = 20;
        $this->filterAndReload();
    }

    protected function filterAndReload(): void
    {
        $this->entityId = null;
//        $this->filter = ($this->port->hasConnectedMsam()) ? $this->port->getEquipment()->getPhysicalSerial() : '';
        $this->filter = '';
        $this->reload();
    }

    protected function reload(): void
    {
        if(is_null($this->filter)){
            $this->filter = '';
        }
        $this->amount = 20;
        //cambiar la forma en la que se buscan los datos
        if($this->slot){
            $data = $this->cardRepository->findCardBySlotAndMsam($this->msam, $this->slot, $this->filter, $this->amount, $this->page);
        }else{
            $data = $this->cardRepository->findCardWithoutSlotAndMsam($this->filter, $this->amount, $this->page);
        }
        $this->reloadData($data);
        $this->setPortsAmount();
    }

    //mejorar
    protected function setPortsAmount()
    {
        for($i=0;$i<count($this->data);$i++){
            $this->data[$i]['ports'] = $this->portRepository->findByCard($this->data[$i]['id']);
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
    public function onConnectionMsamDetailActivateFull(): void
    {
        $this->onConnectionMsamDetailActivate();
    }

    public function onConnectionMsamDetailDeactivate(): void
    {
        $this->reload();
    }

    #[LiveListener(ConnectionMsamDetail::DEACTIVATE.'_Full')]
    public function onConnectionMsamDetailDeactivateFull(): void
    {
        $this->onConnectionMsamDetailDeactivate();
    }

    #[LiveListener(ConnectionMsamSlotList::SELECTED.'_Full')]
    public function onConnectionMsamSlotListSelectedFull(#[LiveArg] ?int $slot, #[LiveArg] ?Msam $msam): void
    {
        $this->slot = $slot;
        $this->msam = $msam;
        $this->filterAndReload();
    }

}