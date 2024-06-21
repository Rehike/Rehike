<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class SRV extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::SRV;
    }

    public function getPriority(): ?int
    {
        return $this->data['pri'] ?? null;
    }

    public function getWeight(): ?int
    {
        return $this->data['weight'] ?? null;
    }

    public function getPort(): ?int
    {
        return $this->data['port'] ?? null;
    }

    public function getTarget(): ?string
    {
        return $this->data['target'] ?? null;
    }

}
