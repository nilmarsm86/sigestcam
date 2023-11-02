<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorPortList;
use App\Components\Live\Traits\ComponentActiveInactive;
use App\Entity\Enums\ConnectionType;
use App\Entity\Msam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/slot_list.html.twig')]
class ConnectionMsamSlotList
{
    use DefaultActionTrait;
    use ComponentActiveInactive;
    use ComponentToolsTrait;

    const SELECTED = self::class.'_selected';

    #[LiveProp]
    public ?array $slots = null;

    #[LiveProp]
    public ?int $selected = null;

    #[LiveProp]
    public ?array $forSelect = null;

    #[LiveProp]
    public ?Msam $msam = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Get selected event name
     * @return string
     */
    protected function getSelectedEventName(): string
    {
        return static::SELECTED.'_'.$this->connection->name;
    }

    #[LiveAction]
    public function select(#[LiveArg] ?int $number, #[LiveArg] ?int $msamId): void
    {
        $this->selected = $number;
        foreach($this->slots as $slot){
            if($slot['number'] === $number){
                $slot['isSelectable'] = false;
            }else{
                $slot['isSelectable'] = true;
            }
        }

        $this->emit($this->getSelectedEventName(), [
            'slot' => $number,
            'msam' => $msamId
        ]);
    }

    /**
     * Recollect ports info for commutator
     * @param Msam $msam
     * @return array
     */
    protected function slotInfo(Msam $msam): array
    {
        //$this->forSelect = PortType::forSelect();
        $slots = [];
        foreach(range(1, $msam->getSlotAmount()) as $slot){
            $slots[$slot] = $this->slotData($slot, $msam);
        }

        return $slots;
    }

    /**
     * Recollect port info
     * @param int $slot
     * @return array
     */
    protected function slotData(int $slot, Msam $msam): array
    {
        $data = [];
        $data['number'] = $slot;
        $data['connection'] = 'bg-gradient-danger';

        $data['isSelectable'] = ($slot === $this->selected) ? false : true;

        $cards = $msam->getCards();
        $data['card'] = false;
        foreach ($cards as $card){
            if($card->getSlot() === $slot && $card->getMsam()->getId() === $msam->getId()){
                $data['card'] = true;
                $data['connection'] = 'bg-gradient-success';
            }
        }

        return $data;
    }

    protected function onConnectionMsamTableDetail(Msam $entity): void
    {
        $this->msam = $entity;
        $this->slots = $this->slotInfo($entity);
        $this->selected = null;
    }

    #[LiveListener(ConnectionMsamTable::DETAIL.'_Full')]
    public function onConnectionMsamTableDetailDirect(#[LiveArg] Msam $entity): void
    {
        $this->onConnectionMsamTableDetail($entity);
    }

    /**
     * Update table from filter, amount or page
     * @return void
     */
    protected function onConnectionMsamTableChange(): void
    {
        $this->slots = null;
    }

    #[LiveListener(ConnectionMsamTable::CHANGE.'_Full')]
    public function onConnectionMsamTableChangeFull(): void
    {
        $this->onConnectionMsamTableChange();
    }

    #[LiveListener(ConnectionCommutatorPortList::SELECTED.'_Full')]
    public function onConnectionCommutatorPortListSelectedFull(): void
    {
        $this->slots = null;
    }
}