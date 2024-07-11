<?php

namespace BlueLibraries\Dns\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class NSEC extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::NSEC;
    }

    public function getNextAuthoritativeName(): ?string
    {
        return $this->data['next-authoritative-name'] ?? null;
    }

    public function getTypes(): ?string
    {
        return $this->data['types'] ?? null;
    }

}
