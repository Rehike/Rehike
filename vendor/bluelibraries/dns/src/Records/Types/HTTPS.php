<?php

namespace BlueLibraries\Dns\Records\Types;

use BlueLibraries\Dns\Records\AbstractRecord;
use BlueLibraries\Dns\Records\RecordTypes;

/**
 * This is known also as TYPE65
 */
class HTTPS extends AbstractRecord
{
    public function getTypeId(): int
    {
        return RecordTypes::HTTPS;
    }

    public function getSeparator(): ?string
    {
        return $this->data['separator'] ?? null;
    }

    public function getOriginalLength(): ?int
    {
        return $this->data['original-length'] ?? null;
    }

    public function getData(): ?string
    {
        return $this->data['data'] ?? null;
    }

}
