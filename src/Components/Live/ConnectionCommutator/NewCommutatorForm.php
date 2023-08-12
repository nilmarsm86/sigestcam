<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
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

#[AsLiveComponent(template: 'components/live/connection_commutator/new_commutator_form.html.twig')]
class NewCommutatorForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = 'new_commutator_form:form:success';
    const MODAL_CLOSE = 'modal-form:close';

    #[LiveProp]
    public ?Commutator $commut = null;

    #[LiveProp(writable: true)]
    public ?string $province = null;

    #[LiveProp(writable: true)]
    public ?string $municipality = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(CommutatorType::class, $this->commut, [
            'province' => (int) $this->province,
            'municipality' => (int) $this->municipality
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
            $commutatorRepository->save($commutator, true);

            $this->emitSuccess([
                'commutator' => $commutator->getId(),
            ]);
        }
    }

    private function mapped(MunicipalityRepository $municipalityRepository, Commutator $commutator): Commutator
    {
        $commutator->setMunicipality($municipalityRepository->find($this->municipality));
        return $commutator;
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