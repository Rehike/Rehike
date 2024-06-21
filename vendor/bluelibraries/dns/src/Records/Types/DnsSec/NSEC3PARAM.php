<?php

namespace BlueLibraries\Dns\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class NSEC3PARAM extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::NSEC3PARAM;
    }

    public function getAlgorithm(): ?int
    {
        return $this->data['algorithm'] ?? null;
    }

    public function getFlags(): ?int
    {
        return $this->data['flags'] ?? null;
    }

    public function getIterations(): ?int
    {
        return $this->data['iterations'] ?? null;
    }

    public function getSalt(): ?string
    {
        return $this->data['salt'] ?? null;
    }

}
