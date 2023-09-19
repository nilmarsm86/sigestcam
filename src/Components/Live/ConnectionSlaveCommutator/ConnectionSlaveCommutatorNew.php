<?php

namespace App\Components\Live\ConnectionSlaveCommutator;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorNew;
use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use App\Form\CommutatorType;
use App\Repository\CommutatorRepository;
use App\Repository\MunicipalityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_slave_commutator/new.html.twig')]
class ConnectionSlaveCommutatorNew extends ConnectionCommutatorNew
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = self::class.'_form_success';
    const MODAL_CLOSE = 'modal_form_close';

    #[LiveProp(updateFromParent: true)]
    public ?Port $masterPort = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommutatorType::class, $this->commut, [
            'province' => (int) $this->province,
            'municipality' => (int) $this->municipality,
            'crud' => true
        ]);
    }

    #[LiveAction]
    public function save(CommutatorRepository $commutatorRepository, MunicipalityRepository $municipalityRepository)
    {
        $this->submitForm();

        if($this->isSubmitAndValid()){
            //lanzar evento a JS
            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $commutator = $this->mapped($municipalityRepository, $this->getForm()->getData());
            $commutator->setMasterCommutator($this->masterPort->getCommutator());
            $commutator->setMunicipality($this->masterPort->getCommutator()->getMunicipality());
            $commutatorRepository->save($commutator, true);

            $this->emitSuccess([
                'commutator' => $commutator->getId(),
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