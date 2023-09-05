<?php

namespace App\Components\Live\ConnectionModem;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use App\Form\ModemType;
use App\Repository\ModemRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_modem/new.html.twig')]
class ConnectionModemNew extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = self::class.'_form_success';
    const MODAL_CLOSE = 'modal_form_close';

    #[LiveProp]
    public ?Modem $masterModem = null;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Modem $mod = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ModemType::class, $this->masterModem);
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->port = null;
        $this->mod = null;
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(ModemRepository $modemRepository)
    {
        $this->submitForm();

        if($this->isSubmitAndValid()){
            /** @var Modem $modem */
            $modem = $this->getForm()->getData();
            if(!is_null($this->port)){
                $modem->setPort($this->port);
                $modem->setMunicipality($this->port->getCommutator()->getMunicipality());
            }

//            if(!is_null($this->masterModem)){
//                $modem->setMasterModem($this->masterModem);
//                $modem->setMunicipality($this->masterModem->getCommutator()->getMunicipality());
//            }

            $modemRepository->save($modem, true);

            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $this->emitSuccess([
                'modem' => $modem->getId()
            ]);
        }
    }

    /**
     * Get form success event name
     * @return string
     */
    private function getSuccessFormEventName(): string
    {
        return static::FORM_SUCCESS.'_'.$this->connection->name;
    }

}