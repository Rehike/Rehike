<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

class NAPTR extends AbstractRecord
{

    public function getTypeId(): int
    {
        return RecordTypes::NAPTR;
    }

    public function getOrder(): ?int
    {
        return $this->data['order'] ?? null;
    }

    public function getPreference(): ?int
    {
        return $this->data['pref'] ?? null;
    }

    public function getFlag(): ?string
    {
        return $this->data['flag'] ?? null;
    }

    public function getServices(): ?string
    {
        return $this->data['services'] ?? null;
    }

    public function getRegex(): ?string
    {
        return $this->data['regex'] ?? null;
    }

    public function getReplacement(): ?string
    {
        return $this->data['replacement'] ?? null;
    }

}

