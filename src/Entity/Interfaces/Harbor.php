<?php

namespace App\Entity\Interfaces;

interface Harbor
{
    public function createPorts(int $amount): static;
}
