<?php

namespace App\Form\Models;

use App\Entity\Enums\State as StateEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class CommutatorFormModel
{
    public function __construct(
        #[Assert\Ip(message:'Establezca un IP válido para el switch.')]
        #[Assert\NotBlank(options: null, message: 'El IP del switch no debe estar vacío.')]
        #[Assert\NotNull(message: 'El IP del switch no debe ser nulo.')]
        public ?string $ip = null,

        #[Assert\NotBlank(message: 'El IP gateway del switch no debe estar vacío.')]
        #[Assert\NotNull(message: 'El IP gateway del switch no debe ser nulo.')]
        #[Assert\Ip(message:'Establezca un IP gateway válido para el switch.')]
        public ?string $gateway = null,

        #[Assert\NotBlank(message: 'Establezca la cantidad de puertos del switch.')]
        #[Assert\NotNull(message: 'La cantidad de puertos del switch no debe ser nula.')]
        #[Assert\Positive]
        public ?int $portsAmount = null,

        #[Assert\NotBlank(message: 'La dirección física no debe estar vacía.')]
        #[Assert\NotNull(message: 'La dirección física no debe ser nula.')]
        public ?string $physicalAddress = null,

        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_\-\.]+$/',
            message: 'El número de serie físico solo debe contener letras, números, guiones y punto.',
        )]
        public ?string $physicalSerial = null,

        #[Assert\Choice(choices: [StateEnum::Active,StateEnum::Inactive], message: 'Seleccione un estado válido.')]
        #[Assert\NotNull(message: 'El estado no puede ser nulo.')]
        public ?StateEnum $state = null,

        //#[Assert\NotBlank(message: 'La marca no debe estar vacía.')]
        //#[Assert\NotNull(message: 'La marca no debe ser nula.')]
        public ?string $brand = null,

        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_\-\.]+$/',
            message: 'El número de inventario solo debe contener letras, números, guiones y punto.',
        )]
        public ?string $model = null,

        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_\-\.]+$/',
            message: 'El número de inventario solo debe contener letras, números, guiones y punto.',
        )]
        public ?string           $inventory = null,

        #[Assert\Regex(
            pattern: '/^[a-zA-Z0-9_\-\.]+$/',
            message: 'El contic solo debe contener letras, números, guiones y punto.',
        )]
        public ?string           $contic = null,

        #[Assert\Valid]
        public ?AddressFormModel $address = null,
    ) {
    }


}