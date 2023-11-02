<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Camera;
use App\Entity\Enums\ConnectionType;
use App\Entity\Modem;
use App\Entity\Port;
use App\Form\CameraType;
use App\Repository\CameraRepository;
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

#[AsLiveComponent(template: 'components/live/connection_camera/new.html.twig')]
class ConnectionCameraNew extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = self::class.'_form_success';
    const MODAL_CLOSE = 'modal_form_close';

    #[LiveProp]
    public ?Camera $cam = null;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp]
    public ?Modem $modem = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CameraType::class, $this->cam);
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Direct')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->port = null;
        $this->modem = null;
    }

    /**
     * @throws Exception
     */
    #[LiveAction]
    public function save(CameraRepository $cameraRepository)
    {
        $this->submitForm();

        if($this->isSubmitAndValid()){
            /** @var Camera $camera */
            $camera = $this->getForm()->getData();

            if($this->connection === ConnectionType::Direct || $this->connection === ConnectionType::SlaveSwitch){
                if(!is_null($this->port)){
                    $camera->setPort($this->port);
                    $camera->setMunicipality($this->port->getCommutator()->getMunicipality());
                }
            }

            if($this->connection === ConnectionType::Simple || $this->connection === ConnectionType::SlaveModem){
                if(!is_null($this->modem)){
                    $camera->setModem($this->modem);
                    if(is_null($this->modem->getMasterModem())){
                        if(is_null($this->modem->getPort())){
                            $this->modem->setPort($this->port);
                        }
                        $camera->setMunicipality($this->modem->getPort()->getCommutator()->getMunicipality());
                    }else{
                        if(is_null($this->modem->getMasterModem()->getPort())){
                            $this->modem->getMasterModem()->setPort($this->port);
                        }
                        $camera->setMunicipality($this->modem->getMasterModem()->getPort()->getCommutator()->getMunicipality());
                    }
                }
            }

            if($this->connection === ConnectionType::Full){
                if(!is_null($this->modem)){
                    $camera->setModem($this->modem);
                    if(is_null($this->modem->getMasterModem())){
                        if(is_null($this->modem->getPort())){
                            $this->modem->setPort($this->port);
                        }

                        if($this->connection->value === ConnectionType::Full->value){
                            $camera->setMunicipality($this->modem->getPort()->getCard()->getMsam()->getPort()->getCommutator()->getMunicipality());
                        }else{
                            $camera->setMunicipality($this->modem->getPort()->getCommutator()->getMunicipality());
                        }
                    }else{
                        if(is_null($this->modem->getMasterModem()->getPort())){
                            $this->modem->getMasterModem()->setPort($this->port);
                        }
                        $camera->setMunicipality($this->modem->getMasterModem()->getPort()->getCommutator()->getMunicipality());
                    }
                }
            }

            $cameraRepository->save($camera, true);

            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $this->emitSuccess([
                'camera' => $camera->getId()
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