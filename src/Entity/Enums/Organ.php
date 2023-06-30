<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum Organ: string
{
    use Enums;

    case Cdr = 'CDR';
}
