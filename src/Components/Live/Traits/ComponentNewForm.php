<?php

namespace App\Components\Live\Traits;

use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ComponentNewForm
{
    #[LiveProp(writable: true)]
    public bool $isSuccessful = false;

    /**
     * @return bool
     */
    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    /**
     * Get form success event name
     * @return string
     */
    private function getSuccessFormEventName(): string
    {
        return ':form_success';
    }

    private function isSubmitAndValid(): bool
    {
        return $this->getForm()->isSubmitted() && $this->getForm()->isValid();
    }

    /**
     * Emit success event for all
     * @param $eventData
     * @return void
     */
    private function emitSuccess($eventData): void
    {
        $this->isSuccessful = true;
        $this->emit($this->getSuccessFormEventName(), $eventData);
        $this->resetForm();
    }
}