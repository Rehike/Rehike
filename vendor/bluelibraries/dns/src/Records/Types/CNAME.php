<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class CNAME extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::CNAME;
    }

    public function getTarget(): ?string
    {
        return $this->data['target'] ?? null;
    }

}
