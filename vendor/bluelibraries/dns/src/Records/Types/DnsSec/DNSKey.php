<?php

namespace BlueLibraries\Dns\Records\Types\DnsSec;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class DNSKey extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::DNSKEY;
    }

    public function getFlags(): ?int
    {
        return $this->data['flags'] ?? null;
    }

    public function getProtocol(): ?int
    {
        return $this->data['protocol'] ?? null;
    }

    public function getAlgorithm(): ?int
    {
        return $this->data['algorithm'] ?? null;
    }

    public function getPublicKey(): ?string
    {
        return $this->data['public-key'] ?? null;
    }

}
