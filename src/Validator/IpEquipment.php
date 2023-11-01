<?php

namespace App\Validator;

use App\Entity\Equipment;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class IpEquipment extends Constraint
{
    /**
     * @var Equipment|null
     */
    public ?Equipment $equipment = null;
}
