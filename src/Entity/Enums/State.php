<?php

namespace App\Entity\Enums;

use App\Entity\Traits\Enums;

enum State: int
{
    use Enums;

    case Active = 1;
    case Inactive = 0;
}
