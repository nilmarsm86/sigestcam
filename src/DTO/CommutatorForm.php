<?php

namespace App\DTO;

use App\Entity\Enums\State as StateEnum;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use Symfony\Component\Validator\Constraints as Assert;

final class CommutatorForm
{
    public function __construct(
        public ?array $commutator = null,
        #[Assert\NotBlank(message: 'El IP no debe estar vacío.')]
        #[Assert\NotNull(message: 'El IP no debe ser nulo.')]
        #[Assert\Ip(message:'Establezca un IP válido.')]
        public ?string $ip = null,
        #[Assert\NotBlank(message: 'Establezca la cantidad de puertos.')]
        #[Assert\NotNull(message: 'La cantidad de puertos no debe ser nula.')]
        #[Assert\Positive]
        public ?int $portsAmount = null,
        #[Assert\NotBlank(message: 'El IP gateway no debe estar vacío.')]
        #[Assert\NotNull(message: 'El IP gateway no debe ser nulo.')]
        #[Assert\Ip(message:'Establezca un IP gateway válido.')]
        public ?string $gateway = null,
        #[Assert\NotBlank(message: 'La dirección física no debe estar vacía.')]
        #[Assert\NotNull(message: 'La dirección física no debe ser nula.')]
        public ?string $physicalAddress = null,
        #[Assert\NotBlank(message: 'El número de serie físico no debe estar vacío.')]
        #[Assert\NotNull(message: 'El número de serie físico no debe ser nulo.')]
        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_\-\.]+$/',
            message: 'El número de serie físico solo debe contener letras, números, guiones y punto.',
        )]
        public ?string $physicalSerial = null,

        #[Assert\Choice(choices: [StateEnum::Active,StateEnum::Inactive], message: 'Seleccione un estado válido.')]
        #[Assert\NotNull(message: 'El estado no puede ser nulo.')]
        public ?StateEnum $state = null,

        #[Assert\Valid]
        public ?AddressForm $address = null
    ) {
        $this->address = new AddressForm();
    }

    public function transform(ProvinceRepository $provinceRepository, MunicipalityRepository $municipalityRepository,)
    {
        $this->ip = $this->commutator['ip'];
        $this->portsAmount = (int) $this->commutator['portsAmount'];
        $this->gateway = $this->commutator['gateway'];
        $this->physicalAddress = $this->commutator['physicalAddress'];
        $this->physicalSerial = $this->commutator['physicalSerial'];
        if(!empty($this->commutator['state'])){
            $this->state = StateEnum::from($this->commutator['state']);
        }

        //$this->address = new AddressForm();
        if($this->commutator['address']['province']){
            $this->address->province = $provinceRepository->find($this->commutator['address']['province']);
        }

        if(isset($this->commutator['address']['municipality'])){
            $this->address->municipality = $municipalityRepository->find($this->commutator['address']['municipality']);
        }
    }


}