<?php

namespace App\Entity\Interfaces;

use App\Entity\Enums\ConnectionType;

interface HarborInterface
{
    /**
     * @param int $amount
     * @param ConnectionType $connectionType
     * @return $this
     */
    public function createPorts(int $amount, ConnectionType $connectionType): static;
}
