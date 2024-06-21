<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class AAAA extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::AAAA;
    }

    public function getIPV6(): ?string
    {
        return $this->data['ipv6'] ?? null;
    }

}