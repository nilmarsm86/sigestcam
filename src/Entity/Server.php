<?php

namespace App\Entity;

use App\Entity\Traits\Connected;
use App\Repository\ServerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
class Server extends ConnectedElement
{
    use Connected;
}
