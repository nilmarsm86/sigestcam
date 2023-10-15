<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Report extends Constraint
{
    public bool $existReport = false;

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, bool $existReport = false)
    {
        parent::__construct($options, $groups, $payload);
        $this->existReport = $existReport;
    }
}
