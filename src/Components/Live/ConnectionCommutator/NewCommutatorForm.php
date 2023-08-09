<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Commutator;
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

    const FORM_SUCCESS = 'new_commutator_form:form:success';
    const MODAL_CLOSE = 'modal-form:close';

    #[LiveProp(writable: true)]
    public bool $isSuccessful = false;

    #[LiveProp]
    public ?Commutator $commut = null;

    #[LiveProp(writable: true)]
    public ?string $province = null;

    #[LiveProp(writable: true)]
    public ?string $municipality = null;

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

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

        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $commutator = $this->mapped($municipalityRepository, $this->getForm()->getData());
            $commutatorRepository->save($commutator, true);

            $this->isSuccessful = true;
            //lanzar evento a lo componentes que lo escucen
            $this->emit(static::FORM_SUCCESS, [
                'commutator' => $commutator->getId(),
            ]);
            //lanzar evento a JS
            $this->dispatchBrowserEvent(static::MODAL_CLOSE/*, [
                'commutator' => $commutator->getId(),
            ]*/);
            $this->resetForm();
        }
    }

    private function mapped(MunicipalityRepository $municipalityRepository, Commutator $commutator): Commutator
    {
        $commutator->setMunicipality($municipalityRepository->find($this->municipality));
        //$commutator->createPorts($commutator->getPortsAmount());
        return $commutator;
    }
}