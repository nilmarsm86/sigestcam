<?php

namespace App\Components\Live\Traits;

use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ComponentActiveInactive
{
    #[LiveProp]
    public ?bool $active = false;
    private mixed $entity;

    /**
     * Get deactivate event name
     * @return string
     */
    private function getDeactivateEventName(): string
    {
        return ':deactivate';
    }

    /**
     * Get deactivate event name
     * @return string
     */
    private function getActivateEventName(): string
    {
        return ':activate';
    }

    #[LiveAction]
    public function activate(#[LiveArg] int $entityId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        $entity->activate();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->active = true;

        $this->emit($this->getActivateEventName(), [
            'entity' => $entity->getId(),
        ]);
    }

    #[LiveAction]
    public function deactivate(#[LiveArg] int $entityId): void
    {
        $entity = $this->entityManager->find($this->entity, $entityId);
        $entity->deactivate();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->active = false;

        $this->emit($this->getDeactivateEventName(), [
            'entity' => $entity->getId(),
        ]);
    }

}