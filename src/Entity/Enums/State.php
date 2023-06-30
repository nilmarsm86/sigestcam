<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum State: string
{
    use Enums;

    case Active = 'Activo';
    case Inactive = 'Inactivo';
}
