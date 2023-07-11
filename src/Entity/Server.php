<?php

namespace App\Entity;

use App\Entity\Traits\Connected;
use App\Repository\ServerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
class Server extends ConnectedElement
{
    use Connected;

    public function __toString(): string
    {
        $data = 'Server: ('.$this->getPhysicalSerial().')';
        if(!is_null($this->getIp())){
            $data .= '['.$this->getIp().']';
        }

        return $data;
    }
}
