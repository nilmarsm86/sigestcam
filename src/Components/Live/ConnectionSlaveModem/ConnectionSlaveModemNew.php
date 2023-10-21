<?php

namespace App\Components\Live\ConnectionSlaveModem;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\ConnectionModem\ConnectionModemNew;
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

#[AsLiveComponent(template: 'components/live/connection_slave_modem/new.html.twig')]
class ConnectionSlaveModemNew extends ConnectionModemNew
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
        return $this->createForm(ModemType::class, null, [
            'slave' => true
        ]);
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
            /** @var Modem $slaveModem */
            $slaveModem = $this->getForm()->getData();
//            if(!is_null($this->port)){
//                $modem->setPort($this->port);
//                $modem->setMunicipality($this->port->getCommutator()->getMunicipality());
//            }

            if(!is_null($this->masterModem)){
                $slaveModem->setMasterModem($this->masterModem);
                $slaveModem->setMunicipality($this->masterModem->getMunicipality());
            }

            $modemRepository->save($slaveModem, true);

            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $this->emitSuccess([
                'modem' => $slaveModem->getId()
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