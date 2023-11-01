<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Card;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Msam;
use App\Entity\Port;
use App\Form\CardType;
use App\Form\MsamType;
use App\Repository\CardRepository;
use App\Repository\MsamRepository;
use App\Repository\MunicipalityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/card_new.html.twig')]
class ConnectionMsamCardNew extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = self::class.'_form_success';
    const MODAL_CLOSE = 'modal_form_close';

    #[LiveProp]
    public ?Msam $msam = null;

    #[LiveProp]
    public ?int $slot = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CardType::class, null, [
            'msam' => $this->msam->getId()
        ]);
    }

    #[LiveListener(ConnectionMsamCardTable::CHANGE.'_Full')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->slot = null;
        $this->msam = null;
    }

    #[LiveAction]
    public function save(CardRepository $cardRepository)
    {
        $this->submitForm();

        if($this->isSubmitAndValid()){
            /** @var Card $card */
            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $card = $this->getForm()->getData();
            if(!is_null($this->slot)){
                $card->setMsam($this->msam);
                $card->setSlot($this->slot);
            }
            $cardRepository->save($card, true);

            $this->emitSuccess([
                'card' => $card->getId(),
            ]);
        }
    }

    /**
     * Get form success event name
     * @return string
     */
    protected function getSuccessFormEventName(): string
    {
        return static::FORM_SUCCESS.'_'.$this->connection->name;
    }
}