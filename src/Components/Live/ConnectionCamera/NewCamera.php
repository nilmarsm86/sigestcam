<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\ConnectionCommutator\CommutatorTable;
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

#[AsLiveComponent(template: 'components/live/connection_camera/new_camera.html.twig')]
class NewCamera extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = 'new_camera_form:form:success';
    const MODAL_CLOSE = 'modal-form:close';

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

    /**
     * Caundo se selecciona un puerto
     * @param Port|null $port
     * @return void

    #[LiveListener(PortList::SELECTED_PORT.':Direct')]
    public function onCommutatorSelectedPortDirect(#[LiveArg] ?Port $port): void
    {
        if(is_null($port->getEquipment())){
            $this->port = $port;
        }else{
            $this->port = null;
        }
    }*/

    /**
     * Caundo se selecciona un modem
     * @param Modem $modem
     * @return void

    //#[LiveListener(PortDetail::SELECTED_PORT.':Direct')]
    public function onModemSelectedDirect(#[LiveArg] Modem $modem): void
    {
        $this->modem = $modem;
    }*/

    /*#[LiveListener(CommutatorTable::SHOW_DETAIL.':Direct')]
    public function onCommutatorTableShowDetailDirect(#[LiveArg] Commutator $entity): void
    {
        $this->port = null;
        $this->modem = null;
    }*/

    #[LiveListener(CommutatorTable::CHANGE_TABLE.':Direct')]
    public function onCommutatorTableChangeDirect(): void
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
            $camera->setMunicipality($this->port->getCommutator()->getMunicipality());
            if(!is_null($this->port)){
                $camera->setPort($this->port);
            }

            if(!is_null($this->modem)){
                $camera->setModem($this->modem);
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
    private function getSuccessFormEventName(): string
    {
        return static::FORM_SUCCESS.':'.$this->connection->name;
    }


}