<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum Priority: string
{
    use Enums;

    case Hight = 'Alta';
    case Medium = 'Media';
    case Low = 'Baja';
}
