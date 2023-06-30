<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum ReportType: string
{
    use Enums;

    case Camera = 'Camare';
    case Modem = 'Modem';
    case Msam = 'Msam';
    case Switch = 'Switch';
}
