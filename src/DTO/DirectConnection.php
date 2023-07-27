<?php

namespace App\DTO;

use App\Entity\Camera;
use App\Entity\Commutator;
use Symfony\Component\Validator\Constraints as Assert;

class DirectConnection
{
    #[Assert\Valid]
    private readonly Commutator $commutator;

    #[Assert\Valid]
    private readonly Camera $camera;


}