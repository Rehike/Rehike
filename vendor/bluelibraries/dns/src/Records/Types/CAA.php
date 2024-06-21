<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class CAA extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::CAA;
    }

    public function getFlags(): ?int
    {
        return $this->data['flags'] ?? null;
    }

    public function getTag(): ?string
    {
        return $this->data['tag'] ?? null;
    }

    public function getValue(): ?string
    {
        return $this->data['value'] ?? null;
    }

}