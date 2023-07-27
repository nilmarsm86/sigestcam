<?php

namespace App\DTO;

use App\Entity\Municipality;
use App\Entity\Province;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

final class AddressForm
{
    #[NotBlank(message: 'Seleccione una provincia.')]
    #[NotNull(message: 'Provincia no nula.')]
    #[Valid]
    public ?Province $province = null;

    #[NotBlank(message: 'Seleccione un municipio.')]
    #[NotNull(message: 'Municipio no nulo.')]
    #[Valid]
    public ?Municipality $municipality = null;
}