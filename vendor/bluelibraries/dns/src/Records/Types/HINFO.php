<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class HINFO extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::HINFO;
    }

    public function getHardware(): ?string
    {
        return $this->data['hardware'] ?? null;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->data['os'] ?? null;
    }

}
