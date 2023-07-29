<?php

namespace App\Components\Live;

use App\DTO\AddressForm;
use App\DTO\CommutatorForm;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/address.html.twig', csrf: false)]
class Address
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public string $formType;

    #[LiveProp(useSerializerForHydration: true)]
    public ?CommutatorForm $data = null;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private readonly ProvinceRepository $provinceRepository,
        private readonly MunicipalityRepository $municipalityRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly RequestStack $request
    )
    {
        $this->shouldAutoSubmitForm = false;
    }

    protected function instantiateForm(): FormInterface
    {
        //if(!is_null($this->data)){
            if(is_null($this->data->address)){
                $this->data->address = new AddressForm();
                if(!empty($this->query)){
                    $this->data->address->province = $this->provinceRepository->find($this->query);
                }
            }else{
                if(!is_null($this->data->address->province)){
                    if($this->data->address->province->getId()){
                        $this->query = $this->data->address->province->getId();
                    }else{
                        if(empty($this->query)){
                            $this->data->address->province = null;
                        }else{
                            if(!empty($this->query)) {
                                $this->data->address->province = $this->provinceRepository->find($this->query);
                            }
                        }
                    }

                    /*if($this->request->getMainRequest()->getMethod() === 'POST'){
                        $data = $this->request->getMainRequest()->request->all('commutator')['address']['municipality'];
                        $this->data->address->municipality = $this->municipalityRepository->find($data);
                    }*/
                }else{
                    if(!empty($this->query)) {
                        $this->data->address->province = $this->provinceRepository->find($this->query);
                    }
                }

                if(!is_null($this->data->address->municipality)){
                    if(!$this->data->address->municipality->getId()){
                        $this->data->address->municipality = null;
                    }
                }
            }
        //}

        $form = $this->formFactory->create($this->formType, $this->data, [
            //'create_from_component' => true,
        ]);
        $form->handleRequest($this->request->getCurrentRequest());

        return $form;
    }

}