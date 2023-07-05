<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum Aim: string
{
    use Enums;

    case Objective = 'Objetivo';
    case NoObjective = 'No objetivo';
}
