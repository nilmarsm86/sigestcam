<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum ReportState: string
{
    use Enums;

    case Open = 'Abierto';
    case Close = 'Cerrado';
}
