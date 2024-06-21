<?php

namespace BlueLibraries\Dns\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class CDS extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::CDS;
    }

    public function getKeyTag(): ?int
    {
        return $this->data['key-tag'] ?? null;
    }

    public function getAlgorithm(): ?int
    {
        return $this->data['algorithm'] ?? null;
    }

    public function getAlgorithmDigest(): ?int
    {
        return $this->data['algorithm-digest'] ?? null;
    }

    public function getDigest(): ?string
    {
        return $this->data['digest'] ?? null;
    }

}
