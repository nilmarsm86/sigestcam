<?php

namespace App\Entity\Interfaces;

use App\Entity\Enums\ConnectionType;

interface HarborInterface
{
    public function createPorts(int $amount, ConnectionType $connectionType): static;
}
