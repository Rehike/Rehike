<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class PTR extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::PTR;
    }

    public function getTarget(): ?string
    {
        return $this->data['target'] ?? null;
    }

}
