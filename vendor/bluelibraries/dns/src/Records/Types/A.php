<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class A extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::A;
    }

    public function getIp(): ?string
    {
        return $this->data['ip'] ?? null;
    }

}
