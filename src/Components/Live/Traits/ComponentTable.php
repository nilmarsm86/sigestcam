<?php

namespace App\Components\Live\Traits;

use App\DTO\Paginator;
use Doctrine\ORM\AbstractQuery;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ComponentTable
{
    #[LiveProp(useSerializerForHydration: true)]
    public ?Paginator $paginator = null;

    #[LiveProp(writable: true)]
    public ?string $filter = '';

    #[LiveProp(writable: true)]
    public ?int $amount = 10;

    #[LiveProp(writable: true)]
    public ?int $page = 1;

    #[LiveProp(writable: true)]
    public mixed $data = null;

    #[LiveProp(writable: true)]
    public ?int $fake = null;

    #[LiveProp]
    public ?int $entityId = null;

    public function mount(): void
    {
        $this->amount = 10;
        $this->filter = '';
        $this->page = 1;
        $this->reload();
    }

    protected function reloadData(mixed $data): void
    {
        $this->paginator = new Paginator($data, $this->amount, $this->page);
        $this->data = $data->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        $this->fake = $data->count();
    }

    /**
     * Get change table event name
     * @return string
     */
    protected function getChangeTableEventName(): string
    {
        return ':change:table';
    }

    /**
     * Get show detail event name
     * @return string
     */
    protected function getShowDetailEventName(): string
    {
        return ':show:detail';
    }

    #[LiveAction]
    public function changeAmount(): void
    {
        $this->page = 1;
        $this->reload();
        $this->emit($this->getChangeTableEventName());
    }

    #[LiveAction]
    public function changeFilter(): void
    {
        $this->page = 1;
        $this->reload();
        $this->emit($this->getChangeTableEventName());
    }

    #[LiveAction]
    public function go(#[LiveArg] int $page): void
    {
        $this->page = $page;
        $this->reload();
        $this->emit($this->getChangeTableEventName());
    }

    #[LiveAction]
    public function detail(#[LiveArg] int $entityId): void
    {
        $this->reload();
        $this->entityId = $entityId;
        $this->emit($this->getShowDetailEventName(), [
            'entity' => $entityId
        ]);
    }

}