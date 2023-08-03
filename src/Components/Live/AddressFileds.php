<?php

namespace App\Components\Live;

use App\Form\Models\AddressFormModel;
use App\Form\Models\CommutatorFormModel;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[AsLiveComponent(template: 'components/live/address.html.twig', csrf: false)]
class AddressFileds
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ValidatableComponentTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public string $formType;

    #[LiveProp(useSerializerForHydration: true, updateFromParent: true)]
    #[Assert\Valid]
    public ?CommutatorFormModel $data = null;

    #[LiveProp(writable: true)]
    public string $province = '';

    #[LiveProp(writable: true)]
    public string $municipality = '';

    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $request
    )
    {
        $this->shouldAutoSubmitForm = false;
        $this->validatedFields = ['data.address.province', 'data.address.municipality'];
    }

    protected function instantiateForm(): FormInterface
    {
        //if(!is_null($this->data)){
            if(is_null($this->data->address)){
                $this->data->address = new AddressFormModel();
                if(!empty($this->province)){
                    $this->data->address->province = $this->provinceRepository->find($this->province);
                }
            }else{
                if(!is_null($this->data->address->province)){
                    if($this->data->address->province->getId()){
                        $this->province = $this->data->address->province->getId();
                    }else{
                        if(empty($this->province)){
                            $this->data->address->province = null;
                        }else{
                            if(!empty($this->province)) {
                                $this->data->address->province = $this->provinceRepository->find($this->province);
                            }
                        }
                    }

                    /*if($this->request->getMainRequest()->getMethod() === 'POST'){
                        $data = $this->request->getMainRequest()->request->all('commutator')['address']['municipality'];
                        $this->data->address->municipality = $this->municipalityRepository->find($data);
                    }*/
                }else{
                    if(!empty($this->province)) {
                        $this->data->address->province = $this->provinceRepository->find($this->province);
                    }
                }

                if(!is_null($this->data->address->municipality)){
                    if(!$this->data->address->municipality->getId()){
                        $this->data->address->municipality = null;
                    }
                }

                if(!empty($this->municipality)) {
                    $this->data->address->municipality = $this->municipalityRepository->find($this->municipality);
                }
            }
        //}

        if($this->province && $this->municipality){
            $this->emit('productAdded', [
                'province' => $this->data?->address?->province?->getId(),
                'municipality' => $this->data?->address?->municipality?->getId(),
            ]);
        }

        $form = $this->formFactory->create($this->formType, $this->data);

        if(!$this->province){
            foreach ($this->getErrors('data.address.province') as $error){
                $form->get('address')->get('province')->addError(new FormError($error));
            }
        }

        if(!$this->municipality){
            foreach ($this->getErrors('data.address.municipality') as $error){
                $form->get('address')->get('municipality')->addError(new FormError($error));
            }
        }

        return $form;
    }

}