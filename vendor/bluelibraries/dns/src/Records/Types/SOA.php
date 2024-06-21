<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class SOA extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::SOA;
    }

    public function getMasterNameServer(): ?string
    {
        return $this->data['mname'] ?? null;
    }

    public function getRawEmailName(): ?string
    {
        return $this->data['rname'] ?? null;
    }

    public function getAdministratorEmailAddress(): ?string
    {
        if (
            empty($this->data)
            || empty($this->data['rname'])
            || !is_string($this->data['rname'])
        ) {
            return null;
        }

        $parts = explode('.', $this->data['rname']);
        $partsLength = count($parts);

        if ($partsLength < 3) {
            return null;
        }

        $result = '';

        foreach ($parts as $key => $part) {
            $separator = $key === 0 ?
                ''
                : ($key === ($partsLength - 2) ? '@' : '.');
            $result .= $separator . $part;
        }

        return $result;
    }


    public function getSerial(): ?int
    {
        return $this->data['serial'] ?? null;
    }

    public function getRefresh(): ?int
    {
        return $this->data['refresh'] ?? null;
    }

    public function getRetry(): ?int
    {
        return $this->data['retry'] ?? null;
    }

    public function getExpire(): ?int
    {
        return $this->data['expire'] ?? null;
    }

    public function getMinimumTtl(): ?int
    {
        return $this->data['minimum-ttl'] ?? null;
    }

}
