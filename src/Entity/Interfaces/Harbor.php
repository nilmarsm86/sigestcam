<?php

namespace App\Entity\Interfaces;

use App\Entity\Enums\ConnectionType;

interface Harbor
{
    public function createPorts(int $amount, ConnectionType $connectionType): static;
}
