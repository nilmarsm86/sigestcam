<?php

namespace App\Components\Live;

use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\Models\AddressFormModel;
use App\Form\Models\CommutatorFormModel as CommutatorFormModel;
use App\Repository\CommutatorRepository;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/commutator_form.html.twig')]
class CommutatorForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public string $formType;

    #[LiveProp(useSerializerForHydration: true)]
    public ?CommutatorFormModel $data = null;

    #[LiveProp(writable: true)]
    public bool $isSuccessful = false;

    public string $type = 'component';

    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $requestStack
    )
    {
        $this->validatedFields = ['ip', 'gateway', 'portsAmount', 'physicalAddress', 'physicalSerial', 'state', 'brand', 'model', 'inventory', 'contic'];
        $this->data = new CommutatorFormModel(address: new AddressFormModel());
    }

    protected function instantiateForm(): FormInterface
    {

        return $this->formFactory->create($this->formType, $this->data);
    }

    public function hasValidationErrors(): bool
    {
        return $this->getForm()->isSubmitted() && !$this->getForm()->isValid();
    }

    #[LiveAction]
    public function save(CommutatorRepository $commutatorRepository)
    {
        if(isset($this->formValues['address']['province'])){
            $this->data->address->province = $this->provinceRepository->find($this->formValues['address']['province']);
        }
        if(isset($this->formValues['address']['municipality'])){
            $this->data->address->municipality = $this->municipalityRepository->find($this->formValues['address']['municipality']);
        }

        $this->submitForm();

        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $commutator = new Commutator($this->data->ip, $this->data->portsAmount, ConnectionType::Direct);
            $commutator->setBrand($this->data->brand);
            $commutator->setContic($this->data->contic);
            $commutator->setGateway($this->data->gateway);
            $commutator->setInventory($this->data->inventory);
            $commutator->setModel($this->data->model);
            $commutator->setMunicipality($this->data->address->municipality);
            $commutator->setPhysicalAddress($this->data->physicalAddress);

            $commutatorRepository->save($commutator, true);

            $this->isSuccessful = true;
            $this->requestStack->getSession()->getFlashBag()->add('success', 'Switch agregado');
            return $this->redirectToRoute('conection_direct', ['filter'=>$this->data->ip]);
        }
    }

    #[LiveListener('productAdded')]
    public function incrementProductCount(#[LiveArg] ?Province $province, #[LiveArg] ?Municipality $municipality)
    {
        if(!empty($province)) {
            $this->data->address->province = $province;
            $this->formValues['address']['province'] = $province->getId();
        }

        if(!empty($municipality)) {
            $this->data->address->municipality = $municipality;
            $this->formValues['address']['municipality'] = $municipality->getId();
        }
    }

    #[LiveAction]
    public function save2()
    {

    }

}